<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'Unauthorized access.');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$teacher_id = $user['user_id'];
$course_id = (int) $_GET['course_id'] ?? 0;

if (!$course_id) {
    setFlashMessage('error', 'Invalid course.');
    redirect('courses.php');
}

// Insert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    validateCSRFToken($_POST['csrf']);
    executeNonQuery("INSERT INTO lessons (course_id, title, content, sequence_order, duration)
                     VALUES (?, ?, ?, ?, ?)", [
        $course_id, sanitize($_POST['title']), sanitize($_POST['content']),
        (int)$_POST['sequence_order'], (int)$_POST['duration']
    ]);
    setFlashMessage('success', 'Lesson added.');
    redirect("lessons.php?course_id=$course_id");
}

// Delete
if (isset($_GET['delete'])) {
    $lesson_id = (int) $_GET['delete'];
    executeNonQuery("DELETE FROM lessons WHERE lesson_id = ? AND course_id = ?", [$lesson_id, $course_id]);
    setFlashMessage('success', 'Lesson deleted.');
    redirect("lessons.php?course_id=$course_id");
}

// View
$lessons = executeQueryAll("SELECT * FROM lessons WHERE course_id = ? ORDER BY sequence_order", [$course_id]);
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
                    <h2>Lessons for Course #<?= $course_id ?></h2>
<form method="POST">
    <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">

    <br>
    <input type="hidden" name="action" value="insert">
    <input type="text" name="title" placeholder="Lesson Title" required>
    <br>
    <textarea name="content" placeholder="Content"></textarea>
    <br>
    <input type="number" name="sequence_order" placeholder="Order" required>
    <br>
    <input type="number" name="duration" placeholder="Minutes">
    <button type="submit">Add Lesson</button>
</form>

<ul>
    <?php foreach ($lessons as $lesson): ?>
        <li>
            <?= htmlspecialchars($lesson['title']) ?> (<?= $lesson['duration'] ?> mins)
            <a href="?course_id=<?= $course_id ?>&delete=<?= $lesson['lesson_id'] ?>">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

                </div>

            </main>
        </div>
    </div>
</body>
</html>


