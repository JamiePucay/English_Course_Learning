<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Get database connection
$conn = db()->getConnection();

// Check if user is logged in and is an admin
if (!Auth::isLoggedIn() || !Auth::hasRole('admin')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();

// Process teacher assignment form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_teacher'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        setFlashMessage('error', 'Invalid security token');
        redirect('courses.php');
    }

    $course_id = sanitize($_POST['course_id']);
    $teacher_id = sanitize($_POST['teacher_id']);
    
    if (empty($course_id) || empty($teacher_id)) {
        setFlashMessage('error', 'Course ID and Teacher ID are required');
    } else {
        try {
            // Check if this assignment already exists
            $checkStmt = $conn->prepare("SELECT * FROM course_teachers WHERE course_id = ? AND teacher_id = ?");
            $checkStmt->execute([$course_id, $teacher_id]);
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                // Assignment already exists, update is_active status if needed
                if (!$row['is_active']) {
                    $updateStmt = $conn->prepare("UPDATE course_teachers SET is_active = 1 WHERE course_id = ? AND teacher_id = ?");
                    $updateStmt->execute([$course_id, $teacher_id]);
                    setFlashMessage('success', 'Teacher assignment reactivated successfully');
                } else {
                    setFlashMessage('info', 'This teacher is already assigned to this course');
                }
            } else {
                // Create new assignment
                $stmt = $conn->prepare("INSERT INTO course_teachers (course_id, teacher_id) VALUES (?, ?)");
                $stmt->execute([$course_id, $teacher_id]);
                setFlashMessage('success', 'Teacher assigned to course successfully');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'Error assigning teacher: ' . $e->getMessage());
        }
    }
    
    redirect('courses.php');
}

    // Process teacher removal if requested
if (isset($_GET['action']) && $_GET['action'] === 'remove_teacher' && isset($_GET['course_id']) && isset($_GET['teacher_id'])) {
    // Verify CSRF token
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        setFlashMessage('error', 'Invalid security token');
        redirect('courses.php');
    }
    
    $course_id = sanitize($_GET['course_id']);
    $teacher_id = sanitize($_GET['teacher_id']);
    
    try {
        // Instead of deleting, set is_active to 0
        $stmt = $conn->prepare("UPDATE course_teachers SET is_active = 0 WHERE course_id = ? AND teacher_id = ?");
        $stmt->execute([$course_id, $teacher_id]);
        setFlashMessage('success', 'Teacher removed from course successfully');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error removing teacher: ' . $e->getMessage());
    }
    
    redirect('courses.php');
}

// Get all teachers for dropdowns
$teachersQuery = $conn->prepare("SELECT user_id, username, first_name, last_name FROM users WHERE role = 'teacher' AND status = 'active' ORDER BY last_name, first_name");
$teachersQuery->execute();
$teachers = $teachersQuery->fetchAll(PDO::FETCH_ASSOC);

// Get courses with no assigned teachers
$noTeacherQuery = "
    SELECT c.* 
    FROM courses c
    LEFT JOIN course_teachers ct ON c.course_id = ct.course_id AND ct.is_active = 1
    WHERE ct.course_teacher_id IS NULL
    ORDER BY c.created_at DESC
";
$noTeacherStmt = $conn->query($noTeacherQuery);
$noTeacherCourses = $noTeacherStmt->fetchAll(PDO::FETCH_ASSOC);

// Get all courses with their assigned teachers
$coursesQuery = "
    SELECT c.course_id, c.course_code, c.title, c.level, c.description, 
           c.created_at, c.status, c.credits, c.duration,
           u.first_name AS creator_first_name, u.last_name AS creator_last_name,
           GROUP_CONCAT(DISTINCT CONCAT(t.first_name, ' ', t.last_name) SEPARATOR ', ') AS assigned_teachers,
           GROUP_CONCAT(DISTINCT t.user_id SEPARATOR ',') AS teacher_ids
    FROM courses c
    JOIN users u ON c.creator_id = u.user_id
    LEFT JOIN course_teachers ct ON c.course_id = ct.course_id AND ct.is_active = 1
    LEFT JOIN users t ON ct.teacher_id = t.user_id
    GROUP BY c.course_id
    HAVING assigned_teachers IS NOT NULL
    ORDER BY c.created_at DESC
";
$coursesStmt = $conn->query($coursesQuery);
$coursesWithTeachers = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Create a new CSRF token
$csrf_token = createCSRFToken();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Course Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Course Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                    </div>
                </div>

                <?php displayFlashMessage('success'); ?>
                <?php displayFlashMessage('error'); ?>
                <?php displayFlashMessage('info'); ?>

                <!-- Courses with no assigned teachers -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Courses with No Assigned Teachers
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($noTeacherCourses) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Title</th>
                                            <th>Level</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Assign Teacher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($noTeacherCourses as $course): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                                <td>
                                                    <a href="view_course.php?id=<?php echo $course['course_id']; ?>">
                                                        <?php echo htmlspecialchars($course['title']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo ucfirst(htmlspecialchars($course['level'])); ?></td>
                                                <td><?php echo htmlspecialchars($course['duration']); ?> weeks</td>
                                                <td>
                                                    <span class="badge badge-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst(htmlspecialchars($course['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($course['created_at']); ?></td>
                                                <td>
                                                    <form method="post" action="" class="form-inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                                        <div class="input-group">
                                                            <select name="teacher_id" class="form-control form-control-sm" required>
                                                                <option value="">Select Teacher</option>
                                                                <?php foreach ($teachers as $teacher): ?>
                                                                    <option value="<?php echo $teacher['user_id']; ?>">
                                                                        <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <button type="submit" name="assign_teacher" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-user-plus"></i> Assign
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle mr-2"></i>
                                All courses have assigned teachers.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- All courses with assigned teachers -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Courses with Assigned Teachers
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($coursesWithTeachers) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Title</th>
                                            <th>Level</th>
                                            <th>Creator</th>
                                            <th>Assigned Teachers</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($coursesWithTeachers as $course): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                                <td>
                                                    <a href="view_course.php?id=<?php echo $course['course_id']; ?>">
                                                        <?php echo htmlspecialchars($course['title']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo ucfirst(htmlspecialchars($course['level'])); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($course['creator_first_name'] . ' ' . $course['creator_last_name']); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $teacherNames = explode(', ', $course['assigned_teachers']);
                                                    $teacherIds = explode(',', $course['teacher_ids']);
                                                    
                                                    foreach ($teacherNames as $index => $teacherName) {
                                                        echo '<div class="d-flex justify-content-between mb-1 align-items-center">';
                                                        echo htmlspecialchars($teacherName);
                                                        echo ' <a href="courses.php?action=remove_teacher&course_id=' . $course['course_id'] . '&teacher_id=' . $teacherIds[$index] . '&csrf_token=' . $csrf_token . '" 
                                                                class="btn btn-sm btn-outline-danger ml-2" 
                                                                onclick="return confirm(\'Are you sure you want to remove this teacher from the course?\')">';
                                                        echo '<i class="fas fa-user-minus"></i></a>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                    <form method="post" action="" class="form-inline mt-2">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                                        <div class="input-group input-group-sm">
                                                            <select name="teacher_id" class="form-control form-control-sm" required>
                                                                <option value="">Add Teacher</option>
                                                                <?php foreach ($teachers as $teacher): ?>
                                                                    <option value="<?php echo $teacher['user_id']; ?>">
                                                                        <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <div class="input-group-append">
                                                                <button type="submit" name="assign_teacher" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst(htmlspecialchars($course['status'])); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No courses with assigned teachers found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>