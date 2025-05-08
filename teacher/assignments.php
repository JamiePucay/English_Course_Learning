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
    executeNonQuery("INSERT INTO assignments (course_id, title, description, max_points, due_date, sequence_order, created_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?)", [
        $course_id, sanitize($_POST['title']), sanitize($_POST['description']),
        (int)$_POST['max_points'], $_POST['due_date'], (int)$_POST['sequence_order'], $teacher_id
    ]);
    setFlashMessage('success', 'Assignment added.');
    redirect("assignments.php?course_id=$course_id");
}

// Delete
if (isset($_GET['delete'])) {
    $assignment_id = (int) $_GET['delete'];
    executeNonQuery("DELETE FROM assignments WHERE assignment_id = ? AND course_id = ?", [$assignment_id, $course_id]);
    setFlashMessage('success', 'Assignment deleted.');
    redirect("assignments.php?course_id=$course_id");
}

$assignments = executeQueryAll("SELECT * FROM assignments WHERE course_id = ? ORDER BY sequence_order", [$course_id]);
?>

<h2>Assignments for Course #<?= $course_id ?></h2>
<form method="POST">
    <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
    <input type="hidden" name="action" value="insert">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="number" name="max_points" placeholder="Points" required>
    <input type="datetime-local" name="due_date">
    <button type="submit">Add Assignment</button>
</form>

<ul>
    <?php foreach ($assignments as $a): ?>
        <li>
            <?= htmlspecialchars($a['title']) ?> (<?= $a['max_points'] ?> pts)
            <a href="?course_id=<?= $course_id ?>&delete=<?= $a['assignment_id'] ?>">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>
