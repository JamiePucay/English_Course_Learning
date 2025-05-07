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
        setFlashMessage('error', 'Course not found or you do not have permission to edit this course.');
        redirect('courses.php');
    }

    // Handle form submission to update course
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
    <title>Edit Course - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <div class="profile-container mt-4">
                    <h2>Edit Course</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" placeholder="Course Title" required>
                        <select name="level" required>
                            <option value="beginner" <?= $course['level'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="elementary" <?= $course['level'] === 'elementary' ? 'selected' : '' ?>>Elementary</option>
                            <option value="intermediate" <?= $course['level'] === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="upper-intermediate" <?= $course['level'] === 'upper-intermediate' ? 'selected' : '' ?>>Upper-Intermediate</option>
                            <option value="advanced" <?= $course['level'] === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                            <option value="proficiency" <?= $course['level'] === 'proficiency' ? 'selected' : '' ?>>Proficiency</option>
                        </select>
                        <select name="status" required>
                            <option value="active" <?= $course['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $course['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <button type="submit">Update Course</button>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
