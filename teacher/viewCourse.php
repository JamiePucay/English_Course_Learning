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

    // Fetch course details
    $course = executeQuerySingle("SELECT * FROM courses WHERE course_id = ? AND creator_id = ?", [$course_id, $teacher_id]);

    if (!$course) {
        setFlashMessage('error', 'Course not found or you do not have permission to view this course.');
        redirect('courses.php');
    }
} else {
    setFlashMessage('error', 'Course ID is missing.');
    redirect('courses.php');
}



$sql = "
SELECT c.* FROM courses c
LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
WHERE c.course_id = ?
AND (
    c.creator_id = :teacher_id
    OR ct.teacher_id = :teacher_id
    OR cs.shared_with_id = :teacher_id
)
";
$course = executeQuerySingle($sql, [
    'course_id' => $course_id,
    'teacher_id' => $teacher_id
]);


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

                <div class="profile-container mt-4">
                    <h2>View Course</h2>
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <p><strong>Level:</strong> <?= htmlspecialchars($course['level']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($course['status']) ?></p>
                    <p><strong>Created At:</strong> <?= formatDate($course['created_at']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($course['description'])) ?></p>

                    <a href="courses.php" class="btn btn-primary">Back to Courses</a>
                </div>

            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
