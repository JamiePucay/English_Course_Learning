<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is already logged in
if (!Auth::isLoggedIn()) {
    redirect('../login.php'); // Redirect to login if not logged in
}

$user = Auth::getCurrentUser(); // Get current logged-in user

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lessons</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

            <div class="profile-container mt-4">
                <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                <img src="../public/images/default-profile.jpg" alt="Default Profile" class="profile-picture">
                <?php endif; ?>

                <div class="profile-info">
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Role:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                    <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></p>
                    <p><strong>Date Registered:</strong> <?= htmlspecialchars($user['date_registered']) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($user['status'])) ?></p>
                    <p><strong>Last Login:</strong> <?= htmlspecialchars($user['last_login']) ?? 'Never' ?></p>
                </div>
            </div>
        </main>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
