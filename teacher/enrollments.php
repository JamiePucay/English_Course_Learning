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
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enrollments <?php echo APP_NAME ?></title>
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
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">Pending Enrollments</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pendingByCourse)): ?>
                                <?php foreach ($pendingByCourse as $courseId => $students): ?>
                                    <div class="mb-3">
                                        <h6 class="text-primary"><?= htmlspecialchars($students[0]['course_title'] ?? getCourseTitle($courses, $courseId)) ?></h6>
                                        <ul class="list-group">
                                            <?php foreach ($students as $student): ?>
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                                            <small class="text-muted">Enrolled on <?= formatDate($student['enrollment_date']) ?></small>
                                                        </div>
                                                        <form action="handleEnrollmentAction.php" method="POST" class="d-flex align-items-center gap-2">
                                                            <input type="hidden" name="enrollment_id" value="<?= $student['enrollment_id'] ?>">
                                                            <div class="btn-group" role="group">
                                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </li>

                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No pending enrollments.</p>
                            <?php endif; ?>
                        </div>
                    </div> 
                </div>
            </main>
        </div>
    </div>    

</body>
</html>