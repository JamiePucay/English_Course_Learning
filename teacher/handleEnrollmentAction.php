<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'Unauthorized access');
    redirect('../user/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($enrollment_id && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'ongoing' : 'dropped';
        
        $sql = "UPDATE enrollments SET status = :status WHERE enrollment_id = :enrollment_id";
        $result = executeQuerySingle($sql, [
            'status' => $status,
            'enrollment_id' => $enrollment_id
        ]);

        setFlashMessage('success', "Enrollment has been " . ($action === 'approve' ? "approved." : "rejected."));
    } else {
        setFlashMessage('error', 'Invalid request.');
    }

    redirect('index.php');
} else {
    setFlashMessage('error', 'Invalid request method.');
    redirect('index.php');
}
