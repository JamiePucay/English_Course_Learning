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

// Check if a quiz ID is provided for viewing a specific quiz
$quiz_id = isset($_GET['quiz_id']) && is_numeric($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

// If quiz_id is provided, handle the quiz taking process
if ($quiz_id) {
    // Check if the quiz exists and belongs to the course
    $quiz = db()->fetchOne(
        "SELECT q.* FROM quizzes q 
         WHERE q.quiz_id = :quiz_id AND q.course_id = :course_id AND q.status = 'active'",
        ['quiz_id' => $quiz_id, 'course_id' => $course_id]
    );
    
    if (!$quiz) {
        setFlashMessage('error', 'Quiz not found or not available');
        redirect("viewCourses.php?id={$course_id}");
    }
    
    // Check if student has access to this quiz
    $access = db()->fetchOne(
        "SELECT * FROM student_quiz_access 
         WHERE student_id = :student_id AND quiz_id = :quiz_id AND is_accessible = 1",
        ['student_id' => $student_id, 'quiz_id' => $quiz_id]
    );
    
    if (!$access) {
        setFlashMessage('error', 'You do not have access to this quiz yet');
        redirect("viewCourses.php?id={$course_id}");
    }
    
    // Check if the student has already reached the maximum attempts allowed
    $attempts_count = db()->fetchOne(
        "SELECT COUNT(*) as count FROM quiz_attempts 
         WHERE student_id = :student_id AND quiz_id = :quiz_id",
        ['student_id' => $student_id, 'quiz_id' => $quiz_id]
    );
    
    if ($attempts_count['count'] >= $quiz['attempts_allowed']) {
        setFlashMessage('error', 'You have reached the maximum number of attempts allowed for this quiz');
        redirect("viewCourses.php?id={$course_id}");
    }
    
    // Check if there's an ongoing attempt
    $ongoing_attempt = db()->fetchOne(
        "SELECT * FROM quiz_attempts 
         WHERE student_id = :student_id AND quiz_id = :quiz_id AND status = 'in_progress'",
        ['student_id' => $student_id, 'quiz_id' => $quiz_id]
    );
    
    $attempt_id = null;
    $time_remaining = 0;
    $questions = [];
    
    if ($ongoing_attempt) {
        // Continue with the existing attempt
        $attempt_id = $ongoing_attempt['attempt_id'];
        
        // Calculate time remaining
        $start_time = strtotime($ongoing_attempt['start_time']);
        $time_limit_seconds = $quiz['time_limit'] * 60;
        $elapsed_seconds = time() - $start_time;
        $time_remaining = $time_limit_seconds - $elapsed_seconds;
        
        // If time has expired, update the attempt status
        if ($time_remaining <= 0) {
            db()->query(
                "UPDATE quiz_attempts SET status = 'timed_out', end_time = :end_time 
                 WHERE attempt_id = :attempt_id",
                ['attempt_id' => $attempt_id, 'end_time' => date('Y-m-d H:i:s', $start_time + $time_limit_seconds)]
            );
            
            setFlashMessage('error', 'Your quiz time has expired');
            redirect("quiz_results.php?attempt_id={$attempt_id}");
        }
    } else {
        // Create a new attempt
        $current_attempt_number = $attempts_count['count'] + 1;
        
        $attempt_id = db()->query(
            "INSERT INTO quiz_attempts (quiz_id, student_id, start_time, attempt_number) 
             VALUES (:quiz_id, :student_id, :start_time, :attempt_number)",
            [
                'quiz_id' => $quiz_id,
                'student_id' => $student_id,
                'start_time' => date('Y-m-d H:i:s'),
                'attempt_number' => $current_attempt_number
            ]
        );
        
        // Set full time for a new attempt
        $time_remaining = $quiz['time_limit'] * 60;
    }
    
    // Get all questions for this quiz
    if ($quiz['is_randomized']) {
        // Get randomized questions
        $questions = db()->fetchAll(
            "SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY RAND()",
            ['quiz_id' => $quiz_id]
        );
    } else {
        // Get questions in sequence order
        $questions = db()->fetchAll(
            "SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY question_id",
            ['quiz_id' => $quiz_id]
        );
    }
    
    // Get quiz content/instructions
    $quiz_content = db()->fetchAll(
        "SELECT * FROM quiz_content WHERE quiz_id = :quiz_id ORDER BY sequence_order",
        ['quiz_id' => $quiz_id]
    );
    
    // Process form submission (when student submits answers)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
        $score = 0;
        $total_points = 0;
        
        // Process each question response
        foreach ($questions as $question) {
            $question_id = $question['question_id'];
            $total_points += $question['points'];
            
            // Handle different question types
            switch ($question['question_type']) {
                case 'multiple_choice':
                case 'true_false':
                    if (isset($_POST["question_{$question_id}"])) {
                        $selected_option_id = $_POST["question_{$question_id}"];
                        
                        // Check if the selected option is correct
                        $option = db()->fetchOne(
                            "SELECT * FROM answer_options 
                             WHERE option_id = :option_id AND question_id = :question_id",
                            ['option_id' => $selected_option_id, 'question_id' => $question_id]
                        );
                        
                        $is_correct = $option && $option['is_correct'];
                        $points_earned = $is_correct ? $question['points'] : 0;
                        $score += $points_earned;
                        
                        // Save the response
                        db()->query(
                            "INSERT INTO student_responses 
                             (attempt_id, question_id, selected_option_id, is_correct, points_earned) 
                             VALUES (:attempt_id, :question_id, :selected_option_id, :is_correct, :points_earned)",
                            [
                                'attempt_id' => $attempt_id,
                                'question_id' => $question_id,
                                'selected_option_id' => $selected_option_id,
                                'is_correct' => $is_correct ? 1 : 0,
                                'points_earned' => $points_earned
                            ]
                        );
                    }
                    break;
                    
                case 'fill_blank':
                case 'essay':
                    if (isset($_POST["question_{$question_id}_text"])) {
                        $text_response = $_POST["question_{$question_id}_text"];
                        
                        // For essay questions, store the response for manual grading
                        db()->query(
                            "INSERT INTO student_responses 
                             (attempt_id, question_id, text_response) 
                             VALUES (:attempt_id, :question_id, :text_response)",
                            [
                                'attempt_id' => $attempt_id,
                                'question_id' => $question_id,
                                'text_response' => $text_response
                            ]
                        );
                    }
                    break;
                    
                case 'matching':
                    // Process matching question type
                    // This would typically involve multiple selections that need to be stored
                    // For simplicity, we're not implementing the full matching logic here
                    break;
            }
        }
        
        // Calculate percentage score
        $percentage_score = $total_points > 0 ? ($score / $total_points) * 100 : 0;
        
        // Update the quiz attempt with the final score and status
        db()->query(
            "UPDATE quiz_attempts SET 
             status = 'completed', 
             end_time = :end_time, 
             score = :score 
             WHERE attempt_id = :attempt_id",
            [
                'end_time' => date('Y-m-d H:i:s'),
                'score' => $percentage_score,
                'attempt_id' => $attempt_id
            ]
        );
        
        // Redirect to results page
        redirect("quiz_results.php?attempt_id={$attempt_id}");
    }
} else {
    // If no quiz_id is provided, show a list of available quizzes for this course
    $quizzes = db()->fetchAll(
        "SELECT q.*, sqa.is_accessible 
         FROM quizzes q
         LEFT JOIN student_quiz_access sqa ON q.quiz_id = sqa.quiz_id AND sqa.student_id = :student_id
         WHERE q.course_id = :course_id
         ORDER BY q.sequence_order",
        ['course_id' => $course_id, 'student_id' => $student_id]
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($quiz) ? htmlspecialchars($quiz['title']) : 'Course Quizzes'; ?> - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="viewCourses.php?id=<?php echo $course_id; ?>"><?php echo htmlspecialchars($course['title']); ?></a></li>
                        <?php if (isset($quiz)): ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($quiz['title']); ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">Quizzes</li>
                        <?php endif; ?>
                    </ol>
                </nav>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php clearFlashMessage(); ?>
                <?php endif; ?>
                
                <?php if (isset($quiz)): ?>
                    <!-- Quiz Taking Interface -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4><?php echo htmlspecialchars($quiz['title']); ?></h4>
                        </div>
                        <div class="card-body">
                            <!-- Quiz Timer -->
                            <div class="quiz-timer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Time Remaining: <span id="timer"></span></h5>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#submitConfirmModal">
                                            Submit Quiz
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quiz Content/Instructions -->
                            <?php if (!empty($quiz_content)): ?>
                                <div class="quiz-content">
                                    <h5>Quiz Instructions</h5>
                                    <?php foreach ($quiz_content as $content): ?>
                                        <div class="mb-3">
                                            <h6><?php echo htmlspecialchars($content['content_title']); ?></h6>
                                            
                                            <?php if ($content['content_type'] == 'text'): ?>
                                                <p><?php echo nl2br(htmlspecialchars($content['content_data'])); ?></p>
                                            <?php elseif ($content['content_type'] == 'image'): ?>
                                                <img src="<?php echo htmlspecialchars($content['file_path']); ?>" alt="<?php echo htmlspecialchars($content['content_title']); ?>" class="img-fluid mb-2">
                                            <?php elseif ($content['content_type'] == 'video'): ?>
                                                <video controls class="w-100 mb-2">
                                                    <source src="<?php echo htmlspecialchars($content['file_path']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php elseif ($content['content_type'] == 'document'): ?>
                                                <a href="<?php echo htmlspecialchars($content['file_path']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-file-download"></i> Download Document
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Quiz Questions Form -->
                            <form id="quizForm" method="POST" action="">
                                <?php foreach ($questions as $index => $question): ?>
                                    <div class="question-container">
                                        <h5>Question <?php echo $index + 1; ?> <small class="text-muted">(<?php echo $question['points']; ?> point<?php echo $question['points'] > 1 ? 's' : ''; ?>)</small></h5>
                                        <p><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>
                                        
                                        <!-- Get question content/attachments if any -->
                                        <?php 
                                        $question_content = db()->fetchAll(
                                            "SELECT * FROM question_content WHERE question_id = :question_id",
                                            ['question_id' => $question['question_id']]
                                        );
                                        
                                        foreach ($question_content as $content): ?>
                                            <div class="mb-3">
                                                <h6><?php echo htmlspecialchars($content['content_title']); ?></h6>
                                                
                                                <?php if ($content['content_type'] == 'image'): ?>
                                                    <img src="<?php echo htmlspecialchars($content['file_path']); ?>" alt="<?php echo htmlspecialchars($content['content_title']); ?>" class="img-fluid mb-2">
                                                <?php elseif ($content['content_type'] == 'video'): ?>
                                                    <video controls class="w-100 mb-2">
                                                        <source src="<?php echo htmlspecialchars($content['file_path']); ?>" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                <?php elseif ($content['content_type'] == 'document'): ?>
                                                    <a href="<?php echo htmlspecialchars($content['file_path']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                                                        <i class="fas fa-file-download"></i> View Attachment
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <!-- Get student's previous response, if any -->
                                        <?php 
                                        $existing_response = null;
                                        if ($ongoing_attempt) {
                                            $existing_response = db()->fetchOne(
                                                "SELECT * FROM student_responses 
                                                 WHERE attempt_id = :attempt_id AND question_id = :question_id",
                                                ['attempt_id' => $attempt_id, 'question_id' => $question['question_id']]
                                            );
                                        }
                                        ?>
                                        
                                        <!-- Different input types based on question type -->
                                        <?php if ($question['question_type'] == 'multiple_choice'): ?>
                                            <?php 
                                            $options = db()->fetchAll(
                                                "SELECT * FROM answer_options WHERE question_id = :question_id",
                                                ['question_id' => $question['question_id']]
                                            );
                                            ?>
                                            
                                            <div class="mt-3">
                                                <?php foreach ($options as $option): ?>
                                                    <div class="form-check">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            name="question_<?php echo $question['question_id']; ?>" 
                                                            id="option_<?php echo $option['option_id']; ?>" 
                                                            value="<?php echo $option['option_id']; ?>"
                                                            <?php echo ($existing_response && $existing_response['selected_option_id'] == $option['option_id']) ? 'checked' : ''; ?>
                                                        >
                                                        <label class="form-check-label" for="option_<?php echo $option['option_id']; ?>">
                                                            <?php echo htmlspecialchars($option['option_text']); ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                        <?php elseif ($question['question_type'] == 'true_false'): ?>
                                            <div class="mt-3">
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="question_<?php echo $question['question_id']; ?>" 
                                                        id="option_true_<?php echo $question['question_id']; ?>" 
                                                        value="true"
                                                        <?php 
                                                        if ($existing_response) {
                                                            $true_option = db()->fetchOne(
                                                                "SELECT option_id FROM answer_options 
                                                                 WHERE question_id = :question_id AND option_text = 'True'",
                                                                ['question_id' => $question['question_id']]
                                                            );
                                                            echo ($existing_response['selected_option_id'] == $true_option['option_id']) ? 'checked' : '';
                                                        }
                                                        ?>
                                                    >
                                                    <label class="form-check-label" for="option_true_<?php echo $question['question_id']; ?>">
                                                        True
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="question_<?php echo $question['question_id']; ?>" 
                                                        id="option_false_<?php echo $question['question_id']; ?>" 
                                                        value="false"
                                                        <?php 
                                                        if ($existing_response) {
                                                            $false_option = db()->fetchOne(
                                                                "SELECT option_id FROM answer_options 
                                                                 WHERE question_id = :question_id AND option_text = 'False'",
                                                                ['question_id' => $question['question_id']]
                                                            );
                                                            echo ($existing_response['selected_option_id'] == $false_option['option_id']) ? 'checked' : '';
                                                        }
                                                        ?>
                                                    >
                                                    <label class="form-check-label" for="option_false_<?php echo $question['question_id']; ?>">
                                                        False
                                                    </label>
                                                </div>
                                            </div>
                                            
                                        <?php elseif ($question['question_type'] == 'fill_blank' || $question['question_type'] == 'essay'): ?>
                                            <div class="form-group mt-3">
                                                <textarea 
                                                    class="form-control" 
                                                    name="question_<?php echo $question['question_id']; ?>_text" 
                                                    rows="4"
                                                ><?php echo ($existing_response) ? htmlspecialchars($existing_response['text_response']) : ''; ?></textarea>
                                            </div>
                                            
                                        <?php elseif ($question['question_type'] == 'matching'): ?>
                                            <!-- Matching question type would be more complex -->
                                            <p class="text-muted">Matching questions are not supported in this preview.</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                
                                <input type="hidden" name="attempt_id" value="<?php echo $attempt_id; ?>">
                                <div class="text-center mt-4 mb-4">
                                    <button type="submit" name="submit_quiz" class="btn btn-lg btn-primary">Submit Quiz</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Confirmation Modal -->
                    <div class="modal fade" id="submitConfirmModal" tabindex="-1" role="dialog" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="submitConfirmModalLabel">Confirm Submission</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to submit your quiz? Once submitted, you cannot change your answers.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('quizForm').submit();">Submit Quiz</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        // Timer functionality
                        let timeRemaining = <?php echo $time_remaining; ?>;
                        
                        // Initialize the timer
                        updateTimer();
                        const timerInterval = setInterval(updateTimer, 1000);
                        
                        // Auto-save every 30 seconds
                        setInterval(autoSave, 30000);
                    </script>
                    
                <?php else: ?>
                    <!-- Quiz List Interface -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Quizzes for <?php echo htmlspecialchars($course['title']); ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($quizzes)): ?>
                                <div class="alert alert-info">
                                    No quizzes available for this course yet.
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <?php 
                                        // Get attempt information
                                        $attempts = db()->fetchAll(
                                            "SELECT * FROM quiz_attempts 
                                             WHERE student_id = :student_id AND quiz_id = :quiz_id
                                             ORDER BY attempt_number DESC",
                                            ['student_id' => $student_id, 'quiz_id' => $quiz['quiz_id']]
                                        );
                                        
                                        $current_attempts = count($attempts);
                                        $highest_score = 0;
                                        $latest_attempt_status = null;
                                        
                                        if (!empty($attempts)) {
                                            foreach ($attempts as $attempt) {
                                                if (isset($attempt['score']) && $attempt['score'] > $highest_score) {
                                                    $highest_score = $attempt['score'];
                                                }
                                            }
                                            $latest_attempt_status = $attempts[0]['status'];
                                        }
                                        
                                        $can_take_quiz = ($quiz['is_accessible'] && 
                                                        ($current_attempts < $quiz['attempts_allowed'] || 
                                                         ($current_attempts > 0 && $latest_attempt_status == 'in_progress')));
                                        ?>
                                        
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                                                    <?php if (!empty($quiz['description'])): ?>
                                                        <p class="mb-1"><?php echo htmlspecialchars($quiz['description']); ?></p>
                                                    <?php endif; ?>
                                                    <small>
                                                        <strong>Time Limit:</strong> <?php echo $quiz['time_limit']; ?> minutes | 
                                                        <strong>Passing Score:</strong> <?php echo $quiz['passing_score']; ?>% | 
                                                        <strong>Attempts:</strong> <?php echo $current_attempts; ?>/<?php echo $quiz['attempts_allowed']; ?>
                                                        <?php if ($highest_score > 0): ?>
                                                            | <strong>Highest Score:</strong> <?php echo number_format($highest_score, 1); ?>%
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <?php if ($can_take_quiz): ?>
                                                        <a href="quizzes.php?id=<?php echo $course_id; ?>&quiz_id=<?php echo $quiz['quiz_id']; ?>" class="btn btn-primary">
                                                            <?php echo ($current_attempts > 0 && $latest_attempt_status == 'in_progress') ? 'Continue Quiz' : 'Start Quiz'; ?>
                                                        </a>
                                                        <?php else: ?>
                                                        <button class="btn btn-secondary" disabled>
                                                            Quiz Unavailable
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>