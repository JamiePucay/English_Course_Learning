<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'Unauthorized');
    redirect('../user/login.php');
}

$lesson_id = (int) ($_GET['id'] ?? 0);
$lesson = executeQuerySingle("SELECT * FROM lessons WHERE lesson_id = ?", [$lesson_id]);

if (!$lesson) {
    setFlashMessage('error', 'Lesson not found');
    redirect('courses.php');
}

$course_id = $lesson['course_id'];
executeNonQuery("DELETE FROM lessons WHERE lesson_id = ?", [$lesson_id]);

setFlashMessage('success', 'Lesson deleted');
redirect("editCourse.php?id=$course_id");
