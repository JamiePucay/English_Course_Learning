<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is already logged in
if (Auth::isLoggedIn()) {
    // Redirect to appropriate dashboard based on role
    $user = Auth::getCurrentUser();
    switch ($user['role']) {
        case 'admin':
            redirect('../admin/index.php');
            break;
        case 'teacher':
            redirect('../teacher/index.php');
            break;
        case 'student':
            redirect('../student/index.php');
            break;
        case 'librarian':
            redirect('../librarian/index.php');
            break;
        default:
            redirect('../index.php');
    }
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $username = sanitize($_POST['username']);
    $password = $_POST['password']; // Don't sanitize password
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate input
    $errors = [];
    if (empty($username)) {
        $errors[] = 'Username or email is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // Attempt login
    if (empty($errors)) {
        $user = Auth::login($username, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Handle remember me
            if ($remember) {
                // Generate a random token
                $token = bin2hex(random_bytes(32));
                
                // Store token in database with user ID
                $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            
            // Redirect based on role
            $role = $user['role'];
            switch ($role) {
                case 'admin':
                    redirect('../admin/index.php');
                    break;
                case 'teacher':
                    redirect('../teacher/index.php');
                    break;
                case 'student':
                    redirect('../student/index.php');
                    break;
                case 'librarian':
                    redirect('../librarian/index.php');
                    break;
                default:
                    redirect('../index.php');
            }
        } else {
            $errors[] = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Courses</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm mt-5">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Login to Your Account</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="username">Username or Email</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($username) ? $username : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="forgot-password.php">Forgot Password?</a>
                            <p class="mt-2">Don't have an account? <a href="register.php">Register</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo APP_NAME; ?></h5>
                    <p>Providing quality English language education accessible to everyone.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="courses.php" class="text-white">Courses</a></li>
                        <li><a href="contact.php" class="text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
