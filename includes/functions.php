<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';
require_once 'auth.php';

/**
 * Helper functions for the application
 */

/**
 * Sanitize input data
 * 
 * @param mixed $data Input data
 * @return mixed Sanitized data
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message or null if none
 */
function getFlashMessage() {
    $message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : null;
    unset($_SESSION['flash_message']);
    return $message;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    $message = getFlashMessage();
    if ($message) {
        $type = $message['type'];
        $content = $message['message'];
        
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$content}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}

/**
 * Check if request is AJAX
 * 
 * @return boolean
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (empty($date)) {
        return ''; // punyetang nageerror error kaya blanko nalng
    }
    return date($format, strtotime($date));
}


/**
 * Get time elapsed string
 * 
 * @param string $datetime Date and time string
 * @return string Time elapsed string
 */
function timeElapsed($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks without modifying the DateInterval object
    $weeks = floor($diff->d / 7);
    $remainingDays = $diff->d % 7;
    
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => $weeks > 0 ? $weeks : null,  // Use weeks variable instead
        'd' => $remainingDays > 0 ? $remainingDays : null,  // Use remaining days
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    foreach ($string as $k => &$v) {
        if ($k === 'w' || $k === 'd') {
            // Already handled these special cases
            if ($v !== null) {
                $v = $v . ' ' . ($k === 'w' ? 'week' : 'day') . ($v > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        } elseif ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    // Rest of your function remains the same
    if (!$string) {
        return 'just now';
    }

    $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/**
 * Generate pagination links
 * 
 * @param int $totalItems Total number of items
 * @param int $itemsPerPage Items per page
 * @param int $currentPage Current page
 * @param string $baseUrl Base URL for links
 * @return string HTML pagination
 */
function generatePagination($totalItems, $itemsPerPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $pagination = '<nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $pagination .= '<li class="page-item">
                            <a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
    } else {
        $pagination .= '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $pagination .= '<li class="page-item">
                            <a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
    } else {
        $pagination .= '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
    }
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

/**
 * Upload file
 * 
 * @param array $file File from $_FILES
 * @param string $directory Directory to upload to
 * @param array $allowedExtensions Allowed file extensions
 * @param int $maxSize Maximum file size in bytes
 * @return array|bool Array with file info or false if upload failed
 */
function uploadFile($file, $directory, $allowedExtensions = null, $maxSize = null) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Use defaults if not specified
    if ($allowedExtensions === null) {
        $allowedExtensions = ALLOWED_EXTENSIONS;
    }
    
    if ($maxSize === null) {
        $maxSize = MAX_FILE_SIZE;
    }
    
    // Get file extension
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Check file extension
    if (!in_array($fileExt, $allowedExtensions)) {
        return false;
    }
    
    // Check file size
    if ($fileSize > $maxSize) {
        return false;
    }
    
    // Create unique filename
    $newFileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
    $destination = $directory . '/' . $newFileName;
    
    // Check if directory exists and create if not
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($fileTmp, $destination)) {
        return [
            'original_name' => $fileName,
            'new_name' => $newFileName,
            'path' => $destination,
            'extension' => $fileExt,
            'size' => $fileSize
        ];
    }
    
    return false;
}

/**
 * Generate a random string
 * 
 * @param int $length Length of string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Get course completion status
 * 
 * @param int $studentId Student ID
 * @param int $courseId Course ID
 * @return float Completion percentage
 */
function getCourseCompletion($studentId, $courseId) {
    // Get course progress from student_progress table
    $progress = db()->fetch(
        "SELECT progress_percentage, lessons_completed
         FROM student_progress
         WHERE student_id = :student_id 
         AND course_id = :course_id",
        ['student_id' => $studentId, 'course_id' => $courseId]
    );

    // If we have direct progress percentage, return it
    if ($progress && isset($progress['progress_percentage'])) {
        return $progress['progress_percentage'];
    }

    // Fallback calculation if progress_percentage isn't available
    // Get total lessons in the course (directly from lessons table)
    $totalLessons = db()->fetch(
        "SELECT COUNT(lesson_id) as total_lessons 
         FROM lessons 
         WHERE course_id = :course_id",
        ['course_id' => $courseId]
    );
    $totalLessonsCount = $totalLessons['total_lessons'];
    
    if ($totalLessonsCount == 0) {
        return 0;
    }
    
    // Get completed lessons count from lesson_completion table
    $completedLessons = db()->fetch(
        "SELECT COUNT(lc.completion_id) as completed_lessons
         FROM lesson_completion lc
         JOIN lessons l ON lc.lesson_id = l.lesson_id
         WHERE lc.student_id = :student_id 
         AND l.course_id = :course_id",
        ['student_id' => $studentId, 'course_id' => $courseId]
    );
    $completedCount = $completedLessons['completed_lessons'];

    // Calculate completion percentage
    return round(($completedCount / $totalLessonsCount) * 100, 2);
}

/**
 * Check if a user has access to a course
 * 
 * @param int $userId User ID
 * @param int $courseId Course ID
 * @return bool
 */
function hasAccessToCourse($userId, $courseId) {
    $user = db()->fetch("SELECT role FROM users WHERE user_id = :user_id", ['user_id' => $userId]);
    
    if (!$user) {
        return false;
    }
    
    // Admins have access to all courses
    if ($user['role'] == 'admin') {
        return true;
    }
    
    // Teachers have access to their courses
    if ($user['role'] == 'teacher') {
        $course = db()->fetch("SELECT course_id FROM courses WHERE course_id = :course_id AND teacher_id = :teacher_id", 
                             ['course_id' => $courseId, 'teacher_id' => $userId]);
        return !empty($course);
    }
    
    // Students have access to enrolled courses
    if ($user['role'] == 'student') {
        $enrollment = db()->fetch("SELECT enrollment_id FROM enrollments 
                                  WHERE student_id = :student_id AND course_id = :course_id AND status != 'dropped'", 
                                 ['student_id' => $userId, 'course_id' => $courseId]);
        return !empty($enrollment);
    }
    
    return false;
}




// --------------- Added for register.php ------------------
function createCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}


function sanitizeInput($data) {
    $data = trim($data);            // Remove whitespace from the beginning and end
    $data = stripslashes($data);    // Remove backslashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special characters
    return $data;
}


function executeQuerySingle($sql, $params = []) {
    // Get the PDO connection instance
    $pdo = db()->getConnection();
    
    // Prepare the statement using the PDO object
    $stmt = $pdo->prepare($sql);
    
    // Execute the query with parameters
    $stmt->execute($params);
    
    // Fetch and return the result as an associative array
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function registerUser($userData) {
    // Assuming $pdo is your database connection
    $pdo = db()->getConnection();

    // Prepare the SQL query to insert the new user
    $sql = 'INSERT INTO users (username, password, email, first_name, last_name, role, status) 
            VALUES (:username, :password, :email, :first_name, :last_name, :role, :status)';
    
    $stmt = $pdo->prepare($sql);
    
    // Store plain text password (nageerror pag hashed password + katamad mag MD5)
    $plainPassword = $userData['password'];

    // Execute the query with the provided data
    $stmt->execute([
        'username' => $userData['username'],
        'password' => $plainPassword,
        'email' => $userData['email'],
        'first_name' => $userData['first_name'],
        'last_name' => $userData['last_name'],
        'role' => $userData['role'],
        'status' => $userData['status']
    ]);
    
    // Return the last inserted user ID
    return $pdo->lastInsertId();
}


/**
 * Executes a non-query SQL statement (e.g., INSERT, UPDATE, DELETE).
 *
 * @param string $sql The SQL query to execute.
 * @param array $params An associative array of parameters to bind to the query.
 * @return bool True on success, false on failure.
 */
function executeNonQuery($sql, $params = [])
{
    $pdo = db()->getConnection();

    try {
        // Prepare the statement
        $stmt = $pdo->prepare($sql);
        
        // Execute the query with parameters
        return $stmt->execute($params);
    } catch (PDOException $e) {
        // Handle any exceptions (errors)
        error_log("Error executing query: " . $e->getMessage());
        return false;
    }
}




// --------------------for student/courses.php-----------------
/**
 * Helper function to determine badge class for enrollment status
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active':
        case 'ongoing':
            return 'success';
        case 'completed':
            return 'primary';
        case 'pending':
            return 'warning';
        case 'dropped':
            return 'danger';
        default:
            return 'secondary';
    }
}



// ---------for teacher/index.php--------------

function executeQueryAll($sql, $params = []) {
    $pdo = db()->getConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




?>