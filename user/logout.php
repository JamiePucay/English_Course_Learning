<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Call the logout method from Auth class
Auth::logout();

// Set a flash message
setFlashMessage('success', 'You have been logged out successfully');

// Redirect to login page
redirect('login.php');
?>