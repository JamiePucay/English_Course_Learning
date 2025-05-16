<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is logged in and is an admin
if (!Auth::isLoggedIn() || !Auth::hasRole('admin')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();

// Process teacher registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_teacher'])) {
    $userData = [
        'username' => trim($_POST['username']),
        'password' => trim($_POST['password']),
        'email' => trim($_POST['email']),
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'role' => 'teacher',
        'status' => 'active'
    ];
    
    // Basic validation
    $errors = [];
    
    if (empty($userData['username'])) {
        $errors[] = "Username is required";
    }
    
    if (empty($userData['password'])) {
        $errors[] = "Password is required";
    }
    
    if (empty($userData['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($userData['first_name'])) {
        $errors[] = "First name is required";
    }
    
    if (empty($userData['last_name'])) {
        $errors[] = "Last name is required";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Register the new teacher
            $userId = registerUser($userData);
            
            if ($userId) {
                setFlashMessage('success', 'Teacher registered successfully!');
                redirect('users.php');
            } else {
                $errors[] = "Failed to register teacher";
            }
        } catch (PDOException $e) {
            // Check for duplicate username or email
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'username')) {
                    $errors[] = "Username already exists";
                } elseif (strpos($e->getMessage(), 'email')) {
                    $errors[] = "Email already exists";
                } else {
                    $errors[] = "Database error: " . $e->getMessage();
                }
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch all teachers
$pdo = db()->getConnection();
$teacherQuery = $pdo->prepare("SELECT * FROM users WHERE role = 'teacher' ORDER BY last_name, first_name");
$teacherQuery->execute();
$teachers = $teacherQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch all students
$studentQuery = $pdo->prepare("SELECT * FROM users WHERE role = 'student' ORDER BY last_name, first_name");
$studentQuery->execute();
$students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php 
                // Display flash messages
                if (isset($_SESSION['flash_messages'])) {
                    foreach ($_SESSION['flash_messages'] as $type => $message) {
                        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                                {$message}
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>";
                    }
                    unset($_SESSION['flash_messages']);
                }
                
                // Display validation errors
                if (!empty($errors)) {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <ul class='mb-0'>";
                    foreach ($errors as $error) {
                        echo "<li>{$error}</li>";
                    }
                    echo "</ul>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>";
                }
                ?>
                
                <!-- 1st Card: Register a new teacher -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Register a New Teacher</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required
                                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required
                                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <button type="submit" name="register_teacher" class="btn btn-primary">Register Teacher</button>
                        </form>
                    </div>
                </div>
                
                <!-- 2nd Card: Display all registered teachers -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Registered Teachers</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Date Registered</th>
                                        <th>Last Login</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($teachers) > 0): ?>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($teacher['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $teacher['status'] == 'active' ? 'success' : ($teacher['status'] == 'inactive' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($teacher['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($teacher['date_registered']); ?></td>
                                                <td><?php echo $teacher['last_login'] ? htmlspecialchars($teacher['last_login']) : 'Never'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No teachers registered yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- 3rd Card: Display all registered students -->
                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <h4>Registered Students</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Date Registered</th>
                                        <th>Last Login</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($students) > 0): ?>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $student['status'] == 'active' ? 'success' : ($student['status'] == 'inactive' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($student['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($student['date_registered']); ?></td>
                                                <td><?php echo $student['last_login'] ? htmlspecialchars($student['last_login']) : 'Never'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No students registered yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>