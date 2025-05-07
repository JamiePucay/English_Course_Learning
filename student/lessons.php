<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a student
if (!Auth::isLoggedIn() || !Auth::hasRole('student')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$student_id = $user['user_id'];

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('error', 'Invalid course ID');
    redirect('index.php');
}

$course_id = $_GET['id'];

$course = db()->fetchOne(
    "SELECT c.*, u.first_name, u.last_name, e.status as enrollment_status, e.enrollment_date
     FROM courses c
     LEFT JOIN course_teachers ct ON c.course_id = ct.course_id
     LEFT JOIN users u ON ct.teacher_id = u.user_id
     LEFT JOIN enrollments e ON c.course_id = e.course_id AND e.student_id = :student_id
     WHERE c.course_id = :course_id AND (e.status IS NULL OR e.status != 'dropped')",
    ['course_id' => $course_id, 'student_id' => $student_id]
);

if (!$course) {
    setFlashMessage('error', 'Course not found or you are not enrolled in this course');
    redirect('index.php');
}

// Check if a lesson ID is provided for viewing a specific lesson
$lesson_id = isset($_GET['lesson_id']) && is_numeric($_GET['lesson_id']) ? $_GET['lesson_id'] : null;

// Debug - temporarily bypass authentication to help identify the issue
error_log("Course ID: " . $course_id);
error_log("Student ID: " . $student_id);
error_log("Course Data: " . print_r($course, true));

// Get all lessons for this course
$lessons = db()->fetchAll(
    "SELECT l.*, 
        CASE 
            WHEN sla.is_accessible = TRUE THEN 'accessible'
            WHEN l.status = 'locked' THEN 'locked'
            ELSE 'active'
        END AS access_status
     FROM lessons l
     LEFT JOIN student_lesson_access sla ON l.lesson_id = sla.lesson_id AND sla.student_id = :student_id
     WHERE l.course_id = :course_id
     ORDER BY l.sequence_order ASC",
    ['course_id' => $course_id, 'student_id' => $student_id]
);

// Function to check if a lesson is accessible by the student
function isLessonAccessible($lesson, $student_id) {
    // If lesson is active by default and no specific access rule exists
    if ($lesson['status'] === 'active' && $lesson['access_status'] !== 'locked') {
        return true;
    }
    
    // If there's a specific access rule for this student
    if ($lesson['access_status'] === 'accessible') {
        return true;
    }
    
    return false;
}

// Get the current lesson content if a lesson is selected
$current_lesson = null;
$lesson_content = [];

if ($lesson_id) {
    $current_lesson = db()->fetchOne(
        "SELECT l.*, 
            CASE 
                WHEN sla.is_accessible = TRUE THEN 'accessible'
                WHEN l.status = 'locked' THEN 'locked'
                ELSE 'active'
            END AS access_status
         FROM lessons l
         LEFT JOIN student_lesson_access sla ON l.lesson_id = sla.lesson_id AND sla.student_id = :student_id
         WHERE l.lesson_id = :lesson_id AND l.course_id = :course_id",
        ['lesson_id' => $lesson_id, 'course_id' => $course_id, 'student_id' => $student_id]
    );
    
    // Check if lesson exists and is accessible
    if (!$current_lesson) {
        setFlashMessage('error', 'Lesson not found');
        redirect("lessons.php?id=" . $course_id);
    }
    
    if (!isLessonAccessible($current_lesson, $student_id)) {
        setFlashMessage('error', 'This lesson is currently locked');
        redirect("lessons.php?id=" . $course_id);
    }
    
    // Get lesson content/attachments
    $lesson_content = db()->fetchAll(
        "SELECT * FROM lesson_content 
         WHERE lesson_id = :lesson_id 
         ORDER BY sequence_order ASC",
        ['lesson_id' => $lesson_id]
    );
    
    // Record that the student has accessed this lesson (could be used for tracking progress)
    // This is a simple implementation - you might want to add more sophisticated tracking
    db()->query(
        "INSERT INTO student_lesson_access (student_id, lesson_id, is_accessible, unlocked_at)
         VALUES (:student_id, :lesson_id, TRUE, NOW())
         ON DUPLICATE KEY UPDATE unlocked_at = NOW()",
        ['student_id' => $student_id, 'lesson_id' => $lesson_id]
    );
}

// Get previous and next lesson for navigation
$prev_lesson = null;
$next_lesson = null;

if ($lesson_id) {
    // Find the previous accessible lesson
    foreach ($lessons as $index => $lesson) {
        if ($lesson['lesson_id'] == $lesson_id && $index > 0) {
            for ($i = $index - 1; $i >= 0; $i--) {
                if (isLessonAccessible($lessons[$i], $student_id)) {
                    $prev_lesson = $lessons[$i];
                    break;
                }
            }
        }
    }
    
    // Find the next accessible lesson
    foreach ($lessons as $index => $lesson) {
        if ($lesson['lesson_id'] == $lesson_id && $index < count($lessons) - 1) {
            for ($i = $index + 1; $i < count($lessons); $i++) {
                if (isLessonAccessible($lessons[$i], $student_id)) {
                    $next_lesson = $lessons[$i];
                    break;
                }
            }
        }
    }
}

include 'header.php';

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
    <div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar with lesson list -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="mb-0">Teacher: <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                </div>
                <div class="card-body p-0">
                    <div class="list-group">
                        <?php foreach ($lessons as $lesson): ?>
                            <?php $is_accessible = isLessonAccessible($lesson, $student_id); ?>
                            <a href="<?php echo $is_accessible ? "lessons.php?id={$course_id}&lesson_id={$lesson['lesson_id']}" : "#"; ?>" 
                               class="list-group-item list-group-item-action <?php echo ($lesson_id == $lesson['lesson_id']) ? 'active' : ''; ?> <?php echo !$is_accessible ? 'disabled' : ''; ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($lesson['title']); ?></h6>
                                    <?php if (!$is_accessible): ?>
                                        <span class="badge badge-secondary"><i class="fas fa-lock"></i></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($lesson['duration']) && $lesson['duration']): ?>
                                    <small><?php echo $lesson['duration']; ?> min</small>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content area -->
        <div class="col-md-9">
            <?php displayFlashMessage(); ?>
            
            <?php if ($current_lesson): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><?php echo htmlspecialchars($current_lesson['title']); ?></h4>
                        <div>
                            <?php if ($prev_lesson): ?>
                                <a href="lessons.php?id=<?php echo $course_id; ?>&lesson_id=<?php echo $prev_lesson['lesson_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($next_lesson): ?>
                                <a href="lessons.php?id=<?php echo $course_id; ?>&lesson_id=<?php echo $next_lesson['lesson_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Next <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Main lesson content -->
                        <div class="lesson-content mb-4">
                            <?php echo $current_lesson['content']; ?>
                        </div>
                        
                        <!-- Lesson attachments/additional content -->
                        <?php if (count($lesson_content) > 0): ?>
                            <h5 class="mt-4">Lesson Materials</h5>
                            <div class="list-group">
                                <?php foreach ($lesson_content as $content): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <?php if ($content['content_type'] == 'text'): ?>
                                                    <i class="fas fa-file-alt text-primary mr-2"></i>
                                                <?php elseif ($content['content_type'] == 'image'): ?>
                                                    <i class="fas fa-image text-success mr-2"></i>
                                                <?php elseif ($content['content_type'] == 'video'): ?>
                                                    <i class="fas fa-video text-danger mr-2"></i>
                                                <?php elseif ($content['content_type'] == 'document'): ?>
                                                    <i class="fas fa-file-pdf text-warning mr-2"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-file text-secondary mr-2"></i>
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($content['content_title']); ?>
                                            </h6>
                                        </div>
                                        
                                        <?php if ($content['content_type'] == 'text'): ?>
                                            <div class="mt-2">
                                                <?php echo $content['content_data']; ?>
                                            </div>
                                        <?php elseif ($content['content_type'] == 'image'): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($content['file_path']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($content['content_title']); ?>">
                                            </div>
                                        <?php elseif ($content['content_type'] == 'video'): ?>
                                            <div class="mt-2">
                                                <video class="w-100" controls>
                                                    <source src="<?php echo htmlspecialchars($content['file_path']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        <?php elseif ($content['content_type'] == 'document' || $content['content_type'] == 'other'): ?>
                                            <div class="mt-2">
                                                <a href="<?php echo htmlspecialchars($content['file_path']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <h3>Welcome to <?php echo htmlspecialchars($course['title']); ?></h3>
                        <p>Select a lesson from the menu on the left to begin.</p>
                        
                        <?php if (count($lessons) > 0): ?>
                            <?php foreach ($lessons as $lesson): ?>
                                <?php if (isLessonAccessible($lesson, $student_id)): ?>
                                    <a href="lessons.php?id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-primary mt-3">
                                        Start with first lesson: <?php echo htmlspecialchars($lesson['title']); ?>
                                    </a>
                                    <?php break; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No lessons available for this course yet. Please check back later.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>