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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken($_POST['csrf']);
    $title = sanitize($_POST['title']);
    $level = sanitize($_POST['level']);
    $status = 'active'; // Default to active status
    
    // Insert the course with the creator_id as teacher_id
    executeNonQuery("INSERT INTO courses (title, level, creator_id, status, created_at) VALUES (?, ?, ?, ?, NOW())", 
        [$title, $level, $teacher_id, $status]);

    setFlashMessage('success', 'Course created!');
    redirect('courses.php');
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
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>
    <h2>Create a course</h2>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
            <input type="text" name="title" placeholder="Course Title" required>
            <select name="level" required>
                <option value="beginner">Beginner</option>
                <option value="elementary">Elementary</option>
                <option value="intermediate">Intermediate</option>
                <option value="upper-intermediate">Upper-Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="proficiency">Proficiency</option>
            </select>
            <button type="submit">Add Course</button>
        </form>

</body>
</html>