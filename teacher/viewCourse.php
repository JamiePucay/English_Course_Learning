<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}
$user = Auth::getCurrentUser();
$teacher_id = $user['user_id'];
if (isset($_GET['id'])) {
    $course_id = (int) $_GET['id'];
    // allows the teacher edit the course if they are the creator, assigned teacher, or it's shared with them
    $sql = "SELECT c.* FROM courses c
        LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
        LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
        WHERE c.course_id = :course_id
        AND (
            c.creator_id = :teacher_id
            OR ct.teacher_id = :teacher_id
            OR cs.shared_with_id = :teacher_id
        )";
    $course = executeQuerySingle($sql, [
        'course_id' => $course_id,
        'teacher_id' => $teacher_id
    ]);
    if (!$course) {
        setFlashMessage('error', 'Course not found or you do not have permission to edit this course.');
        redirect('courses.php');
    }
    
    // Get all students enrolled in this course
    $enrolled_students_sql = "
        SELECT e.enrollment_id, e.status, e.enrollment_date, e.final_grade, 
               u.user_id, u.first_name, u.last_name, u.email
        FROM enrollments e
        JOIN users u ON e.student_id = u.user_id
        WHERE e.course_id = :course_id
        AND e.status != 'pending' AND e.status != 'dropped'
        ORDER BY u.last_name, u.first_name
    ";
    
    $enrolled_students = executeQueryAll($enrolled_students_sql, ['course_id' => $course_id]);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validateCSRFToken($_POST['csrf']);
        $title = sanitize($_POST['title']);
        $level = sanitize($_POST['level']);
        $status = sanitize($_POST['status']);
        // Update the course
        executeNonQuery("UPDATE courses SET title = ?, level = ?, status = ? WHERE course_id = ?", 
            [$title, $level, $status, $course_id]);
        setFlashMessage('success', 'Course updated!');
        redirect('courses.php');
    }
} else {
    setFlashMessage('error', 'Course ID is missing.');
    redirect('courses.php');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?= htmlspecialchars($course['title']) ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Level:</strong> <?= htmlspecialchars($course['level']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($course['status']) ?></p>
                        <p><strong>Created At:</strong> <?= formatDate($course['created_at']) ?></p>
                        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($course['description'])) ?></p>
                        <a href="editCourse.php?id=<?= $course['course_id'] ?>" class="btn btn-primary">Edit Course</a>
                    </div>
                </div>
                
                <!-- Enrolled Students Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Enrolled Students</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($enrolled_students) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Enrollment Date</th>
                                            <th>Grade</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrolled_students as $student): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                                <td><?= htmlspecialchars($student['email']) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $student['status'] === 'completed' ? 'success' : 'primary' ?>">
                                                        <?= ucfirst(htmlspecialchars($student['status'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($student['enrollment_date']) ?></td>
                                                <td>
                                                    <?php if ($student['final_grade']): ?>
                                                        <?= $student['final_grade'] ?>%
                                                    <?php else: ?>
                                                        <span class="text-muted">Not graded</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="viewStudent.php?id=<?= $student['user_id'] ?>&course_id=<?= $course_id ?>" class="btn btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="updateGrade.php?enrollment_id=<?= $student['enrollment_id'] ?>" class="btn btn-primary">
                                                            <i class="fas fa-edit"></i> Grade
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No students are currently enrolled in this course.
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