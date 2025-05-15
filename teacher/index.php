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

$pageTitle = "Teacher Dashboard";

// Modified function to get course title directly from the database if not found in the local array
function getCourseTitle($courses, $courseId) {
    // First try to find in the local array
    foreach ($courses as $c) {
        if ($c['course_id'] == $courseId) {
            return $c['title'];
        }
    }
    
    // If not found, fetch directly from the database
    $course = db()->fetchOne(
        "SELECT title FROM courses WHERE course_id = :course_id",
        ['course_id' => $courseId]
    );
    
    return $course ? $course['title'] : 'Untitled Course';
}

$sql = "
SELECT DISTINCT c.* 
FROM courses c
LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
WHERE 
    c.creator_id = :teacher_id 
    OR ct.teacher_id = :teacher_id 
    OR cs.shared_with_id = :teacher_id
";
$courses = executeQueryAll($sql, ['teacher_id' => $teacher_id]);

// Get pending enrollments for courses the teacher is involved in
$pendingSql = "
SELECT DISTINCT e.enrollment_id, e.student_id, e.course_id, e.enrollment_date, u.first_name, u.last_name, c.title as course_title
FROM enrollments e
JOIN users u ON u.user_id = e.student_id
JOIN courses c ON c.course_id = e.course_id
LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
WHERE 
    e.status = 'pending' AND (
        c.creator_id = :teacher_id 
        OR ct.teacher_id = :teacher_id 
        OR cs.shared_with_id = :teacher_id
    )
";

$pendingEnrollments = executeQueryAll($pendingSql, ['teacher_id' => $teacher_id]);

// Group pending students by course_id
$pendingByCourse = [];
foreach ($pendingEnrollments as $enroll) {
    $pendingByCourse[$enroll['course_id']][] = $enroll;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

    <div class="mt-4">
        <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?></h2>

        <!-- Courses Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Courses You Handle</h5>
            </div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <div class="row">
                        <?php foreach ($courses as $course): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                        <p class="card-text">
                                            <strong>Status:</strong> <?= htmlspecialchars($course['status']) ?><br>
                                            <strong>Created:</strong> <?= formatDate($course['created_at']) ?>
                                        </p>
                                    </div>
                                    <div class="card-footer text-end bg-white border-top-0">
                                        <a href="viewCourse.php?id=<?= $course['course_id'] ?>" class="btn btn-primary btn-sm">
                                            View Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">You don't have any courses yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>


        </div>
    </div>

</body>


</html>


