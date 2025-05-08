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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'insert') {
    validateCSRFToken($_POST['csrf']);
    executeNonQuery("INSERT INTO quizzes (course_id, title, description, time_limit, passing_score, sequence_order, created_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?)", [
        $course_id, sanitize($_POST['title']), sanitize($_POST['description']),
        (int)$_POST['time_limit'], (int)$_POST['passing_score'],
        (int)$_POST['sequence_order'], $teacher_id
    ]);
    setFlashMessage('success', 'Quiz added.');
    redirect("quizzes.php?course_id=$course_id");
}

// Delete
if (isset($_GET['delete'])) {
    $quiz_id = (int) $_GET['delete'];
    executeNonQuery("DELETE FROM quizzes WHERE quiz_id = ? AND course_id = ?", [$quiz_id, $course_id]);
    setFlashMessage('success', 'Quiz deleted.');
    redirect("quizzes.php?course_id=$course_id");
}

$quizzes = executeQueryAll("SELECT * FROM quizzes WHERE course_id = ? ORDER BY sequence_order", [$course_id]);
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
<h2>Quizzes for Course #<?= $course_id ?></h2>
<form method="POST">
    <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
    <input type="hidden" name="action" value="insert">
    <input type="text" name="title" placeholder="Title" required>
    <br>
    <textarea name="description" placeholder="Description"></textarea>
    <br>
    <input type="number" name="time_limit" placeholder="Time (mins)">
    <br>
    <input type="number" name="passing_score" placeholder="Pass Score (%)">
    <br>
    <input type="number" name="sequence_order" placeholder="Order" required>
    <button type="submit">Add Quiz</button>
</form>
<br>

<ul>
    <?php foreach ($quizzes as $q): ?>
        <li>
            <?= htmlspecialchars($q['title']) ?> (<?= $q['passing_score'] ?>%)
            <a href="?course_id=<?= $course_id ?>&delete=<?= $q['quiz_id'] ?>">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

                </div>

            </main>
        </div>
    </div>
</body>
</html>
