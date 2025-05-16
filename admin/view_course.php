<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to view this course');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$user_id = $user['user_id'];

// Get course ID from URL parameter
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($course_id <= 0) {
    setFlashMessage('error', 'Invalid course ID');
    redirect('../courses/index.php');
}

try {
    $pdo = db()->getConnection();
    
    // Get course details
    $stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name 
                          FROM courses c
                          JOIN users u ON c.creator_id = u.user_id
                          WHERE c.course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        setFlashMessage('error', 'Course not found');
        redirect('../courses/index.php');
    }
    
    // Check if user has access to this course
    if ($course['status'] == 'inactive' && $course['creator_id'] != $user_id && !Auth::hasRole('admin')) {
        setFlashMessage('error', 'This course is currently inactive');
        redirect('../courses/index.php');
    }
    
    if (!Auth::hasRole('admin') && !Auth::hasRole('teacher') && !hasAccessToCourse($user_id, $course_id, $pdo)) {
        setFlashMessage('error', 'You do not have access to this course');
        redirect('../courses/index.php');
    }
    
    // Get lessons
    $stmt = $pdo->prepare("SELECT * FROM lessons 
                          WHERE course_id = ? 
                          ORDER BY sequence_order ASC");
    $stmt->execute([$course_id]);
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get assignments
    $stmt = $pdo->prepare("SELECT * FROM assignments 
                          WHERE course_id = ? 
                          ORDER BY sequence_order ASC");
    $stmt->execute([$course_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get quizzes
    $stmt = $pdo->prepare("SELECT * FROM quizzes 
                          WHERE course_id = ? 
                          ORDER BY sequence_order ASC");
    $stmt->execute([$course_id]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get exams
    $stmt = $pdo->prepare("SELECT * FROM exams 
                          WHERE course_id = ? 
                          ORDER BY sequence_order ASC");
    $stmt->execute([$course_id]);
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get course completion percentage for student
    $completion = 0;
    if (Auth::hasRole('student')) {
        $completion = getCourseCompletion($user_id, $course_id, $pdo);
    }
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Database error: ' . $e->getMessage());
    redirect('../courses/index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($course['title']) ?> - Course Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php displayFlashMessage('success'); ?>
                <?php displayFlashMessage('error'); ?>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h2><?= htmlspecialchars($course['title']) ?> (<?= htmlspecialchars($course['course_code']) ?>)</h2>
                        </div>
                        
                        <!-- Course Information -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <p class="lead"><?= htmlspecialchars($course['description']) ?></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Level:</strong> <?= ucfirst(htmlspecialchars($course['level'])) ?></p>
                                        <p><strong>Credits:</strong> <?= htmlspecialchars($course['credits']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Duration:</strong> <?= htmlspecialchars($course['duration']) ?> weeks</p>
                                        <p><strong>Created by:</strong> <?= htmlspecialchars($course['first_name'] . ' ' . $course['last_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php if (Auth::hasRole('student')): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Your Progress</h5>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $completion ?>%;" 
                                                 aria-valuenow="<?= $completion ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= $completion ?>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Lessons Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Lessons</h4>
                                <?php if (Auth::hasRole('admin') || $course['creator_id'] == $user_id): ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (count($lessons) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($lessons as $lesson): ?>
                                    <a href="view_lesson.php?id=<?= $lesson['lesson_id'] ?>" class="list-group-item list-group-item-action <?= $lesson['status'] == 'locked' ? 'disabled text-muted' : '' ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?= htmlspecialchars($lesson['title']) ?>
                                                <?php if ($lesson['status'] == 'locked'): ?>
                                                <i class="fas fa-lock ml-2"></i>
                                                <?php endif; ?>
                                            </h5>
                                            <?php if ($lesson['duration']): ?>
                                            <small><?= $lesson['duration'] ?> minutes</small>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">No lessons available for this course yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Assignments Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Assignments</h4>
                                <?php if (Auth::hasRole('admin') || $course['creator_id'] == $user_id): ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (count($assignments) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($assignments as $assignment): ?>
                                    <a href="view_assignment.php?id=<?= $assignment['assignment_id'] ?>" class="list-group-item list-group-item-action <?= $assignment['status'] == 'locked' ? 'disabled text-muted' : '' ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?= htmlspecialchars($assignment['title']) ?>
                                                <?php if ($assignment['status'] == 'locked'): ?>
                                                <i class="fas fa-lock ml-2"></i>
                                                <?php endif; ?>
                                            </h5>
                                            <small>
                                                <?php if ($assignment['due_date']): ?>
                                                Due: <?= formatDate($assignment['due_date']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <p class="mb-1">Max Points: <?= $assignment['max_points'] ?></p>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">No assignments available for this course yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quizzes Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Quizzes</h4>
                                <?php if (Auth::hasRole('admin') || $course['creator_id'] == $user_id): ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (count($quizzes) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($quizzes as $quiz): ?>
                                    <a href="view_quiz.php?id=<?= $quiz['quiz_id'] ?>" class="list-group-item list-group-item-action <?= $quiz['status'] == 'locked' ? 'disabled text-muted' : '' ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?= htmlspecialchars($quiz['title']) ?>
                                                <?php if ($quiz['status'] == 'locked'): ?>
                                                <i class="fas fa-lock ml-2"></i>
                                                <?php endif; ?>
                                            </h5>
                                            <small>
                                                <?php if ($quiz['time_limit']): ?>
                                                Time Limit: <?= $quiz['time_limit'] ?> minutes
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <p class="mb-1">
                                            Passing Score: <?= $quiz['passing_score'] ?>% 
                                            &bull; Attempts: <?= $quiz['attempts_allowed'] ?>
                                            <?php if ($quiz['is_randomized']): ?>
                                            &bull; <i class="fas fa-random"></i> Randomized
                                            <?php endif; ?>
                                        </p>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">No quizzes available for this course yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Exams Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Exams</h4>
                                <?php if (Auth::hasRole('admin') || $course['creator_id'] == $user_id): ?>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (count($exams) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($exams as $exam): ?>
                                    <a href="view_exam.php?id=<?= $exam['exam_id'] ?>" class="list-group-item list-group-item-action <?= $exam['status'] == 'locked' || $exam['status'] == 'cancelled' ? 'disabled text-muted' : '' ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?= htmlspecialchars($exam['title']) ?>
                                                <?php if ($exam['status'] == 'locked'): ?>
                                                <i class="fas fa-lock ml-2"></i>
                                                <?php elseif ($exam['status'] == 'cancelled'): ?>
                                                <span class="badge badge-danger">Cancelled</span>
                                                <?php elseif ($exam['status'] == 'completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                                <?php endif; ?>
                                            </h5>
                                            <small>
                                                Date: <?= formatDate($exam['exam_date']) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1">
                                            Duration: <?= $exam['duration'] ?> minutes
                                            &bull; Total Marks: <?= $exam['total_marks'] ?>
                                            &bull; Passing Marks: <?= $exam['passing_marks'] ?>
                                        </p>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p class="text-muted">No exams available for this course yet.</p>
                                <?php endif; ?>
                            </div>
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