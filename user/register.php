<?php
/**
 * Registration page
 */

// Include configuration
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


// Initialize variables
$errors = [];
$formData = [
    'first_name' => '',
    'last_name' => '',
    'username' => '',
    'email' => '',
    'date_of_birth' => '',
    'language_level' => 'beginner'
];

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid form submission');
        redirect('register.php');
    }
    
    // Get form data
    $formData = [
        'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
        'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
        'username' => sanitizeInput($_POST['username'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'date_of_birth' => sanitizeInput($_POST['date_of_birth'] ?? ''),
        'language_level' => sanitizeInput($_POST['language_level'] ?? 'beginner')
    ];
    
    // Validate form data
    if (empty($formData['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($formData['last_name'])) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($formData['username'])) {
        $errors[] = 'Username is required';
    } elseif (strlen($formData['username']) < 4) {
        $errors[] = 'Username must be at least 4 characters long';
    }
    
    if (empty($formData['email'])) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($formData['password'])) {
        $errors[] = 'Password is required';
    } elseif (strlen($formData['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }
    
    if ($formData['password'] !== $formData['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
    
    // Check if username or email already exists
    $sql = 'SELECT COUNT(*) as count FROM users WHERE username = :username OR email = :email';
    $result = executeQuerySingle($sql, [
        'username' => $formData['username'],
        'email' => $formData['email']
    ]);
    
    if ($result && $result['count'] > 0) {
        $errors[] = 'Username or email already exists';
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        $userData = [
            'username' => $formData['username'],
            'password' => $formData['password'],
            'email' => $formData['email'],
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
            'role' => 'student',
            'status' => 'active'
        ];
        
        $userId = registerUser($userData);
        
        if ($userId) {
            // Update student profile with additional data
            $sql = 'UPDATE students SET 
                    date_of_birth = :date_of_birth,
                    language_level = :language_level
                    WHERE user_id = :user_id';
            
            executeNonQuery($sql, [
                'date_of_birth' => $formData['date_of_birth'],
                'language_level' => $formData['language_level'],
                'user_id' => $userId
            ]);
            
            // Set success message and redirect to login
            setFlashMessage('success', 'Registration successful! You can now log in.');
            redirect('login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

// Create CSRF token
$csrfToken = createCSRFToken();

// Page title
$pageTitle = 'Register - ' . APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link btn btn-outline-light" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Create New Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form action="register.php" method="post" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $formData['first_name']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $formData['last_name']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $formData['username']; ?>" required>
                                <small class="form-text text-muted">Username must be at least 4 characters long.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $formData['email']; ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $formData['date_of_birth']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language_level">English Level</label>
                                        <select class="form-control" id="language_level" name="language_level">
                                            <option value="beginner" <?php echo $formData['language_level'] == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                            <option value="elementary" <?php echo $formData['language_level'] == 'elementary' ? 'selected' : ''; ?>>Elementary</option>
                                            <option value="intermediate" <?php echo $formData['language_level'] == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                            <option value="upper_intermediate" <?php echo $formData['language_level'] == 'upper_intermediate' ? 'selected' : ''; ?>>Upper Intermediate</option>
                                            <option value="advanced" <?php echo $formData['language_level'] == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                            <option value="proficient" <?php echo $formData['language_level'] == 'proficient' ? 'selected' : ''; ?>>Proficient</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                                    <label class="custom-control-label" for="terms">I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Create Account</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Already have an account? <a href="login.php">Log In</a></p>
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