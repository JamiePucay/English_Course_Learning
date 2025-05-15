<?php
$user = Auth::getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="sidebar-header p-3 mb-3 border-bottom">
            <h5><?php echo APP_NAME; ?></h5>
            <div class="user-info">
                <div>
                    <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                    <small class="d-block text-muted"><?php echo ucfirst($user['role']); ?></small>
                </div>
            </div>
        </div>

        <ul class="nav flex-column">
            <?php if ($user['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="../admin/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'users.php' && strpos($_SERVER['REQUEST_URI'], 'users') !== false ? 'active' : ''; ?>" href="../admin/users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'courses.php' && strpos($_SERVER['REQUEST_URI'], 'courses') !== false ? 'active' : ''; ?>" href="../admin/courses.php">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>


            <?php elseif ($user['role'] == 'teacher'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="../teacher/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'enrollments.php' ? 'active' : ''; ?>" href="../teacher/enrollments.php">
                        <i class="fas fa-book"></i> Enrollments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'courses.php' ? 'active' : ''; ?>" href="../teacher/courses.php">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>


            <?php elseif ($user['role'] == 'student'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="../student/index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'courses.php' ? 'active' : ''; ?>" href="../student/courses.php">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>
            <?php endif; ?>
            
            <!-- Common menu items for all users -->
            <li class="nav-item">
                <a class="nav-link" href="../user/profile.php">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>