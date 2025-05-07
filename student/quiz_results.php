<?php
/**
 * Quiz Results Page
 * 
 * This script displays the results of a student's quiz attempt.
 */

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

// Check if attempt ID is provided
if (!isset($_GET['attempt_id']) || !is_numeric($_GET['attempt_id'])) {
    setFlashMessage('error', 'Invalid attempt ID');
    redirect('index.php');
}

$attempt_id = $_GET['attempt_id'];

// Get the attempt and verify it belongs to this student
$attempt = db()->fetchOne(
    "SELECT qa.*, q.quiz_id, q.title as quiz_title, q.passing_score, q.time_limit, q.attempts_allowed,
            c.course_id, c.course_name
     FROM quiz_attempts qa 
     JOIN quizzes q ON qa.quiz_id = q.quiz_id 
     JOIN courses c ON q.course_id = c.course_id
     WHERE qa.attempt_id = :attempt_id AND qa.student_id = :student_id",
    ['attempt_id' => $attempt_id, 'student_id' => $student_id]
);

if (!$attempt) {
    setFlashMessage('error', 'Attempt not found or you do not have permission to view it');
    redirect('index.php');
}

// If attempt is still in progress and hasn't timed out, redirect to continue the quiz
if ($attempt['status'] == 'in_progress') {
    $start_time = strtotime($attempt['start_time']);
    $time_limit_seconds = $attempt['time_limit'] * 60;
    $elapsed_seconds = time() - $start_time;
    
    if ($elapsed_seconds <= $time_limit_seconds) {
        redirect("quiz.php?id={$attempt['course_id']}&quiz_id={$attempt['quiz_id']}");
    } else {
        // Time has expired, update the attempt status
        db()->query(
            "UPDATE quiz_attempts SET status = 'timed_out', end_time = :end_time 
             WHERE attempt_id = :attempt_id",
            [
                'attempt_id' => $attempt_id, 
                'end_time' => date('Y-m-d H:i:s', $start_time + $time_limit_seconds)
            ]
        );
        
        // Recalculate score for this attempt
        calculateQuizScore($attempt_id);
        
        // Refresh attempt data
        $attempt = db()->fetchOne(
            "SELECT qa.*, q.quiz_id, q.title as quiz_title, q.passing_score, q.time_limit, q.attempts_allowed,
                    c.course_id, c.course_name
             FROM quiz_attempts qa 
             JOIN quizzes q ON qa.quiz_id = q.quiz_id 
             JOIN courses c ON q.course_id = c.course_id
             WHERE qa.attempt_id = :attempt_id",
            ['attempt_id' => $attempt_id]
        );
    }
}

// Get all questions for this quiz
$questions = db()->fetchAll(
    "SELECT q.*, 
            (SELECT COUNT(*) FROM student_responses sr WHERE sr.question_id = q.question_id AND sr.attempt_id = :attempt_id) as answered
     FROM questions q
     WHERE q.quiz_id = :quiz_id
     ORDER BY q.question_id",
    ['quiz_id' => $attempt['quiz_id'], 'attempt_id' => $attempt_id]
);

// Get student responses for each question
$responses = [];
foreach ($questions as $question) {
    $response = db()->fetchOne(
        "SELECT sr.*, ao.option_text 
         FROM student_responses sr
         LEFT JOIN answer_options ao ON sr.selected_option_id = ao.option_id
         WHERE sr.attempt_id = :attempt_id AND sr.question_id = :question_id",
        ['attempt_id' => $attempt_id, 'question_id' => $question['question_id']]
    );
    
    if ($response) {
        $responses[$question['question_id']] = $response;
    }
}

// Get correct answers for each question
$correct_answers = [];
foreach ($questions as $question) {
    if ($question['question_type'] == 'multiple_choice' || $question['question_type'] == 'true_false') {
        $correct_option = db()->fetchOne(
            "SELECT * FROM answer_options 
             WHERE question_id = :question_id AND is_correct = 1",
            ['question_id' => $question['question_id']]
        );
        
        if ($correct_option) {
            $correct_answers[$question['question_id']] = $correct_option;
        }
    }
}

// Calculate remaining attempts
$attempts_count = db()->fetchOne(
    "SELECT COUNT(*) as count FROM quiz_attempts 
     WHERE student_id = :student_id AND quiz_id = :quiz_id",
    ['student_id' => $student_id, 'quiz_id' => $attempt['quiz_id']]
);

$remaining_attempts = $attempt['attempts_allowed'] - $attempts_count['count'];

/**
 * Calculate and update the quiz score
 * 
 * @param int $attempt_id The attempt ID
 * @return void
 */
function calculateQuizScore($attempt_id) {
    // Get attempt and quiz information
    $attempt_info = db()->fetchOne(
        "SELECT qa.*, q.quiz_id
         FROM quiz_attempts qa 
         JOIN quizzes q ON qa.quiz_id = q.quiz_id 
         WHERE qa.attempt_id = :attempt_id",
        ['attempt_id' => $attempt_id]
    );
    
    if (!$attempt_info) {
        return;
    }
    
    // Get all questions for this quiz
    $questions = db()->fetchAll(
        "SELECT * FROM questions WHERE quiz_id = :quiz_id",
        ['quiz_id' => $attempt_info['quiz_id']]
    );
    
    $total_points = 0;
    $earned_points = 0;
    
    foreach ($questions as $question) {
        $total_points += $question['points'];
        
        // For essay questions, points are assigned manually by teacher
        if ($question['question_type'] == 'essay') {
            $response = db()->fetchOne(
                "SELECT * FROM student_responses 
                 WHERE attempt_id = :attempt_id AND question_id = :question_id",
                ['attempt_id' => $attempt_id, 'question_id' => $question['question_id']]
            );
            
            if ($response && isset($response['points_earned'])) {
                $earned_points += $response['points_earned'];
            }
            
            continue;
        }
        
        // For objective questions (multiple choice, true/false)
        if ($question['question_type'] == 'multiple_choice' || $question['question_type'] == 'true_false') {
            $response = db()->fetchOne(
                "SELECT sr.* FROM student_responses sr
                 WHERE sr.attempt_id = :attempt_id AND sr.question_id = :question_id",
                ['attempt_id' => $attempt_id, 'question_id' => $question['question_id']]
            );
            
            if ($response && isset($response['selected_option_id'])) {
                // Check if the selected option is correct
                $option = db()->fetchOne(
                    "SELECT * FROM answer_options 
                     WHERE option_id = :option_id AND question_id = :question_id",
                    ['option_id' => $response['selected_option_id'], 'question_id' => $question['question_id']]
                );
                
                $is_correct = $option && $option['is_correct'];
                $points_earned = $is_correct ? $question['points'] : 0;
                
                // Update the response with correctness and points
                db()->query(
                    "UPDATE student_responses 
                     SET is_correct = :is_correct, points_earned = :points_earned 
                     WHERE response_id = :response_id",
                    [
                        'is_correct' => $is_correct ? 1 : 0,
                        'points_earned' => $points_earned,
                        'response_id' => $response['response_id']
                    ]
                );
                
                $earned_points += $points_earned;
            }
        }
        
        // Handle other question types (fill_blank, matching) as needed
    }
    
    // Calculate percentage score
    $percentage_score = $total_points > 0 ? ($earned_points / $total_points) * 100 : 0;
    
    // Update the quiz attempt with the final score
    db()->query(
        "UPDATE quiz_attempts SET score = :score 
         WHERE attempt_id = :attempt_id",
        [
            'score' => $percentage_score,
            'attempt_id' => $attempt_id
        ]
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Results - <?php echo htmlspecialchars($attempt['quiz_title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="viewCourse.php?id=<?php echo $attempt['course_id']; ?>"><?php echo htmlspecialchars($attempt['course_name']); ?></a></li>
                        <li class="breadcrumb-item"><a href="quiz.php?id=<?php echo $attempt['course_id']; ?>">Quizzes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Results</li>
                    </ol>
                </nav>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php clearFlashMessage(); ?>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4><?php echo htmlspecialchars($attempt['quiz_title']); ?> - Results</h4>
                    </div>
                    <div class="card-body">
                        <div class="result-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Quiz Information</h5>
                                    <p><strong>Course:</strong> <?php echo htmlspecialchars($attempt['course_name']); ?></p>
                                    <p><strong>Attempt:</strong> <?php echo $attempt['attempt_number']; ?> of <?php echo $attempt['attempts_allowed']; ?></p>
                                    <p><strong>Started:</strong> <?php echo date('M d, Y H:i', strtotime($attempt['start_time'])); ?></p>
                                    <p><strong>Completed:</strong> 
                                        <?php echo $attempt['end_time'] ? date('M d, Y H:i', strtotime($attempt['end_time'])) : 'N/A'; ?>
                                    </p>
                                    <p>
                                        <strong>Status:</strong>
                                        <?php if ($attempt['status'] == 'completed'): ?>
                                            <span class="badge badge-success">Completed</span>
                                        <?php elseif ($attempt['status'] == 'timed_out'): ?>
                                            <span class="badge badge-danger">Timed Out</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">In Progress</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Score Summary</h5>
                                    <div class="text-center">
                                        <div class="display-4">
                                            <?php echo number_format($attempt['score'], 1); ?>%
                                        </div>
                                        <?php if ($attempt['score'] >= $attempt['passing_score']): ?>
                                            <div class="alert alert-success mt-2">
                                                <i class="fas fa-check-circle"></i> Passed (≥<?php echo $attempt['passing_score']; ?>%)
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-danger mt-2">
                                                <i class="fas fa-times-circle"></i> Failed (<<?php echo $attempt['passing_score']; ?>%)
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($remaining_attempts > 0): ?>
                                            <div class="mt-3">
                                                <p>You have <?php echo $remaining_attempts; ?> attempt(s) remaining.</p>
                                                <a href="quiz.php?id=<?php echo $attempt['course_id']; ?>&quiz_id=<?php echo $attempt['quiz_id']; ?>" class="btn btn-primary">
                                                    Retake Quiz
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-4">Question Summary</h5>
                        
                        <?php foreach ($questions as $index => $question): ?>
                            <?php 
                            $response_exists = isset($responses[$question['question_id']]);
                            $is_correct = $response_exists && isset($responses[$question['question_id']]['is_correct']) 
                                        ? $responses[$question['question_id']]['is_correct'] : false;
                            
                            $question_class = '';
                            if ($question['question_type'] != 'essay') {
                                $question_class = $is_correct ? 'correct-answer' : 'incorrect-answer';
                            }
                            ?>
                            <div class="question-container <?php echo $question_class; ?>">
                                <h6>Question <?php echo $index + 1; ?>:</h6>
                                <p><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>
                                
                                <?php if ($question['question_type'] == 'multiple_choice' || $question['question_type'] == 'true_false'): ?>
                                    <ul class="list-unstyled">
                                        <?php
                                        $options = db()->fetchAll(
                                            "SELECT * FROM answer_options WHERE question_id = :question_id",
                                            ['question_id' => $question['question_id']]
                                        );

                                        foreach ($options as $option):
                                            $selected = $response_exists && $responses[$question['question_id']]['selected_option_id'] == $option['option_id'];
                                            $isCorrect = $option['is_correct'];

                                            $optionClass = '';
                                            if ($selected && $isCorrect) {
                                                $optionClass = 'option-correct';
                                            } elseif ($selected && !$isCorrect) {
                                                $optionClass = 'option-incorrect';
                                            } elseif ($isCorrect) {
                                                $optionClass = 'option-correct';
                                            }
                                        ?>
                                            <li class="<?php echo $optionClass; ?>">
                                                <i class="fas <?php echo $selected ? 'fa-check-circle' : 'fa-circle'; ?>"></i>
                                                <?php echo htmlspecialchars($option['option_text']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php elseif ($question['question_type'] == 'essay'): ?>
                                    <p><strong>Your Answer:</strong></p>
                                    <div class="border p-2 bg-light">
                                        <?php 
                                        echo $response_exists ? nl2br(htmlspecialchars($responses[$question['question_id']]['response_text'])) : '<em>No response submitted.</em>'; 
                                        ?>
                                    </div>
                                    <?php if ($response_exists && isset($responses[$question['question_id']]['points_earned'])): ?>
                                        <p class="mt-2"><strong>Points Earned:</strong> <?php echo $responses[$question['question_id']]['points_earned']; ?> / <?php echo $question['points']; ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
