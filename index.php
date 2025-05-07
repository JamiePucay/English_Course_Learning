<?php
/**
 * Homepage 
 */ 

// Include configuration
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Check if user is already logged in
if (Auth::isLoggedIn()) {
    // Redirect to appropriate dashboard based on role
    $user = Auth::getCurrentUser();
    switch ($user['role']) {
        case 'admin':
            redirect('admin/index.php');
            break;
        case 'teacher':
            redirect('teacher/index.php');
            break;
        case 'student':
            redirect('student/index.php');
            break;
        case 'librarian':
            redirect('librarian/index.php');
            break;
        default:
            redirect('index.php');
    }
}

// Page title
$pageTitle = APP_NAME . ' - Learn English Online';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="public/assets/css/style.css">
    <style>
        .jumbotron {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('public/images/english-learning.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        .feature-box {
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 20px;
            color: #2ea64c;
        }
        .bg-primary {
        background-color: #2ea64c !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light" href="user/register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Jumbotron -->
    <div class="jumbotron text-center">
        <div class="container">
            <h1 class="display-4">Welcome to <?php echo APP_NAME; ?></h1>
            <p class="lead">A comprehensive platform for learning English and achieving language proficiency</p>
            <hr class="my-4">
            <p>Join us to learn English and unlock new opportunities! Let’s improve together, step by step!</p>
            <a class="btn btn-light btn-lg" href="register.php">Get Started</a>
            <a class="btn btn-outline-light btn-lg" href="courses.php">View Courses</a>
        </div>
    </div>

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Platform?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Interactive Courses</h4>
                        <p>Engage with interactive lessons designed by language experts to improve all aspects of your English skills.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h4>Certification</h4>
                        <p>Earn globally recognized English proficiency certificates to showcase your language skills.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <h4>Digital Library</h4>
                        <p>Access a vast collection of digital resources to enhance your learning experience and practice.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Expert Teachers</h4>
                        <p>Learn from qualified and experienced English language teachers who provide guidance and feedback.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Progress Tracking</h4>
                        <p>Monitor your learning progress with detailed analytics and performance reports.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box bg-white text-center">
                        <div class="feature-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h4>Learn Anywhere</h4>
                        <p>Access your courses anytime, anywhere with our responsive platform that works on all devices.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Levels -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">English Proficiency Levels</h2>
            <div class="row">
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Beginner</h5>
                            <p class="card-text">Start your English journey with basic vocabulary and simple phrases.</p>
                            <a href="courses.php?level=beginner" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Elementary</h5>
                            <p class="card-text">Build a foundation with essential grammar and everyday English.</p>
                            <a href="courses.php?level=elementary" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Intermediate</h5>
                            <p class="card-text">Expand your skills with more complex language structures.</p>
                            <a href="courses.php?level=intermediate" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Upper-Intermediate</h5>
                            <p class="card-text">Enhance fluency and accuracy in various contexts.</p>
                            <a href="courses.php?level=upper_intermediate" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Advanced</h5>
                            <p class="card-text">Refine your skills with nuanced language and expressions.</p>
                            <a href="courses.php?level=advanced" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Proficient</h5>
                            <p class="card-text">Master English with near-native proficiency and confidence.</p>
                            <a href="courses.php?level=proficient" class="btn btn-outline-primary btn-sm">View Courses</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">What Our Students Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="public/images/default-profile.jpg" alt="Student" class="rounded-circle mr-3" width="60">
                                <div>
                                    <h5 class="mb-0">Ting L</h5>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                            <p class="card-text">"It's different from Sparta Campus Language School. Cafe Campus' learning style is more relaxed and flexible. Here, the pace of learning is less intense and students have more autonomy to control their own study time."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="public/images/default-profile.jpg" alt="Student" class="rounded-circle mr-3" width="60">
                                <div>
                                    <h5 class="mb-0">Yuki Miho</h5>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                            <p class="card-text">"The students here are from a wide range of countries, allowing for a unique opportunity to experience different cultures while studying. I’m staying in a four-person room, and each of my roommates is from a different country, so I get to enjoy cultural exchanges even outside of study time."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="public/images/default-profile.jpg" alt="Student" class="rounded-circle mr-3" width="60">
                                <div>
                                    <h5 class="mb-0">Quân Nguyễn</h5>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                            <p class="card-text">"If you are looking for a place to heal after tiring days of studying or after stressful working hours, then BECI The Cafe Campus is a perfect choice. You can study while immersing yourself in the cool green space, which will make you study much more effectively."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2>Ready to Improve Your English?</h2>
            <p class="lead">Join our platform today and take your English skills to the next level</p>
            <a href="user/register.php" class="btn btn-light btn-lg m-2">Register Now</a>
            <a href="courses.php" class="btn btn-outline-light btn-lg m-2">Browse Courses</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?php echo APP_NAME; ?></h5>
                    <p>Providing quality English language education accessible to everyone, anywhere in the world.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="courses.php" class="text-white">Courses</a></li>
                        <li><a href="contact.php" class="text-white">Contact Us</a></li>
                        <li><a href="privacy.php" class="text-white">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white mr-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="mt-3">
                        <p><i class="fas fa-envelope mr-2"></i> <?php echo APP_EMAIL; ?></p>
                        <p><i class="fas fa-phone-alt mr-2"></i> +1 (123) 456-7890</p>
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