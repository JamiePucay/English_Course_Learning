<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a student
if (!Auth::isLoggedIn() || !Auth::hasRole('student')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$student_id = $user['user_id'];

// Process enrollment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
    
    // Check if already enrolled
    $existingEnrollment = db()->fetch(
        "SELECT * FROM enrollments WHERE student_id = :student_id AND course_id = :course_id",
        ['student_id' => $student_id, 'course_id' => $course_id]
    );
    
    // Get a teacher ID for this course (primary teacher)
    $courseTeacher = db()->fetchOne(
        "SELECT ct.teacher_id 
         FROM course_teachers ct 
         WHERE ct.course_id = :course_id AND ct.is_active = TRUE 
         LIMIT 1",
        ['course_id' => $course_id]
    );
    
    if (!$courseTeacher) {
        // Fallback to course creator if no active teachers
        $courseTeacher = db()->fetchOne(
            "SELECT creator_id as teacher_id 
             FROM courses 
             WHERE course_id = :course_id",
            ['course_id' => $course_id]
        );
    }
    
    if (!$courseTeacher) {
        setFlashMessage('error', 'Cannot enroll: No teacher is assigned to this course.');
        redirect('index.php');
        exit;
    }
    
    $teacher_id = $courseTeacher['teacher_id'];
    
    if ($existingEnrollment) {
        if ($existingEnrollment['status'] === 'dropped') {
            // Reactivate enrollment
            db()->update(
                'enrollments',
                ['status' => 'pending', 'enrollment_date' => date('Y-m-d H:i:s')],
                ['enrollment_id' => $existingEnrollment['enrollment_id']]
            );
            setFlashMessage('success', 'Your enrollment has been reactivated and is pending approval.');
        } else {
            setFlashMessage('info', 'You are already enrolled in this course.');
        }
    } else {
        // Create new enrollment WITH teacher_id
        $enrollmentId = db()->insert('enrollments', [
            'student_id' => $student_id,
            'course_id' => $course_id,
            'teacher_id' => $teacher_id, // Add the teacher_id field
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ]);
    }
    
    redirect('index.php');
}

// Get available courses for enrollment - those the student is not already enrolled in
$availableCourses = db()->fetchAll(
    "SELECT 
        c.course_id,
        c.title,
        c.course_code,
        c.description,
        c.level,
        c.status,
        c.created_at,
        u.first_name,
        u.last_name,
        COUNT(DISTINCT e.enrollment_id) AS student_count,
        GROUP_CONCAT(DISTINCT CONCAT(
            cs.day_of_week, ' ', 
            TIME_FORMAT(cs.start_time, '%H:%i'), '-', 
            TIME_FORMAT(cs.end_time, '%H:%i'), 
            ' at ', cs.location
        ) ORDER BY cs.day_of_week SEPARATOR '; ') AS schedule
    FROM courses c
    JOIN course_teachers ct ON c.course_id = ct.course_id AND ct.is_active = TRUE
    JOIN users u ON ct.teacher_id = u.user_id
    LEFT JOIN course_schedule cs ON cs.course_teacher_id = ct.course_teacher_id
    LEFT JOIN enrollments e 
        ON e.course_id = c.course_id 
        AND e.status != 'dropped'
    WHERE c.status = 'active'
    AND NOT EXISTS (
        SELECT 1 FROM enrollments e2 
        WHERE e2.course_id = c.course_id 
        AND e2.student_id = :student_id 
        AND e2.status != 'dropped'
    )
    GROUP BY c.course_id, ct.teacher_id
    ORDER BY c.title ASC",
    ['student_id' => $student_id]
);


// Filters
$search = $_GET['search'] ?? '';
$level = $_GET['level'] ?? 'all';

if (!empty($search) || $level != 'all') {
    $filteredCourses = [];
    foreach ($availableCourses as $course) {
        $matchSearch = empty($search) || 
                       stripos($course['title'], $search) !== false || 
                       stripos($course['description'], $search) !== false || 
                       stripos($course['course_code'], $search) !== false;
                       
        $matchLevel = $level == 'all' || $course['level'] == $level;
        
        if ($matchSearch && $matchLevel) {
            $filteredCourses[] = $course;
        }
    }
    $availableCourses = $filteredCourses;
}

// Get available levels for filter
$levels = db()->fetchAll("SELECT DISTINCT level FROM courses ORDER BY FIELD(level, 'beginner', 'intermediate', 'advanced', 'proficient')");

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$total = count($availableCourses);
$totalPages = ceil($total / $perPage);
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;

$paginatedCourses = array_slice($availableCourses, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Courses - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Enroll in Courses</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> My Courses
                        </a>
                    </div>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search Courses</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by title, code, description...">
                            </div>
                            <div class="col-md-4">
                                <label for="level" class="form-label">Proficiency Level</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="all" <?php if($level == 'all') echo 'selected'; ?>>All Levels</option>
                                    <?php foreach($levels as $lvl): ?>
                                        <option value="<?php echo $lvl['level']; ?>" <?php if($level == $lvl['level']) echo 'selected'; ?>>
                                            <?php echo ucfirst($lvl['level']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Available Courses -->
                <div class="card">
                    <div class="card-body">
                        <?php if (count($paginatedCourses) > 0): ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                                <?php foreach ($paginatedCourses as $course): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="card-title"><?php echo $course['title']; ?></h5>
                                                <h6 class="card-subtitle text-muted"><?php echo $course['course_code']; ?></h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?php echo substr($course['description'], 0, 150); ?>...</p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><i class="fas fa-user-tie"></i> <?php echo $course['first_name'] . ' ' . $course['last_name']; ?></span>
                                                    <span class="badge bg-info"><?php echo ucfirst($course['level']); ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span><i class="fas fa-calendar-alt"></i>
                                                        <?php if (!empty($course['schedule'])): ?>
                                                        <p><strong>Schedule:</strong><br>
                                                        <?php
                                                        $scheduleItems = explode('; ', $course['schedule']);
                                                        foreach ($scheduleItems as $item) {
                                                            echo '- ' . htmlspecialchars($item) . '<br>';
                                                        }
                                                        ?>
                                                        </p>
                                                        <?php else: ?>
                                                        <p><strong>Schedule:</strong> Not yet scheduled.</p>
                                                    <?php endif; ?></span>
                                                    <span><i class="fas fa-users"></i> <?php echo $course['student_count']; ?> students</span>
                                                </div>
                                            </div>
                                            <div class="card-footer d-grid">
                                                <form action="" method="POST">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                                    <button type="submit" name="enroll" class="btn btn-success w-100">
                                                        <i class="fas fa-sign-in-alt"></i> Enroll Now
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Course pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&level=<?php echo $level; ?>" tabindex="-1">Previous</a>
                                        </li>
                                        
                                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&level=<?php echo $level; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&level=<?php echo $level; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="alert alert-info">
                                No available courses found. Please check back later for new courses.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>