<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'Unauthorized access');
    redirect('../user/login.php');
}

validateCSRFToken($_POST['csrf']);

$content_id = (int) $_POST['content_id'];
$lesson_id = (int) $_POST['lesson_id'];

// Fetch file path to delete from filesystem
$attachment = executeQuerySingle("SELECT file_path FROM lesson_content WHERE content_id = ?", [$content_id]);

if ($attachment) {
    $filePath = UPLOAD_DIR . $attachment['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete the record
    executeNonQuery("DELETE FROM lesson_content WHERE content_id = ?", [$content_id]);
    setFlashMessage('success', 'Attachment deleted successfully.');
} else {
    setFlashMessage('error', 'Attachment not found.');
}

redirect("editLesson.php?id=$lesson_id");


?>
