<?php
/**
 * Quiz Auto-Save Functionality
 * 
 * This script handles AJAX requests for automatically saving student responses
 * during a quiz attempt to prevent data loss.
 */

// Ensure this script is only accessed via AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    exit('Direct access not permitted');
}

// Check if user is logged in and is a student
if (!Auth::isLoggedIn() || !Auth::hasRole('student')) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$user = Auth::getCurrentUser();
$student_id = $user['user_id'];

// Check if we have the required data
if (!isset($_POST['attempt_id']) || !is_numeric($_POST['attempt_id'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid attempt ID']));
}

$attempt_id = $_POST['attempt_id'];

// Verify the attempt belongs to this student
$attempt = db()->fetchOne(
    "SELECT qa.*, q.quiz_id, q.time_limit 
     FROM quiz_attempts qa 
     JOIN quizzes q ON qa.quiz_id = q.quiz_id 
     WHERE qa.attempt_id = :attempt_id AND qa.student_id = :student_id AND qa.status = 'in_progress'",
    ['attempt_id' => $attempt_id, 'student_id' => $student_id]
);

if (!$attempt) {
    http_response_code(404);
    exit(json_encode(['error' => 'Attempt not found or not in progress']));
}

// Check if time has expired
$start_time = strtotime($attempt['start_time']);
$time_limit_seconds = $attempt['time_limit'] * 60;
$elapsed_seconds = time() - $start_time;

if ($elapsed_seconds > $time_limit_seconds) {
    // Time has expired, update the attempt status
    db()->query(
        "UPDATE quiz_attempts SET status = 'timed_out', end_time = :end_time 
         WHERE attempt_id = :attempt_id",
        [
            'attempt_id' => $attempt_id, 
            'end_time' => date('Y-m-d H:i:s', $start_time + $time_limit_seconds)
        ]
    );
    
    http_response_code(410);
    exit(json_encode(['error' => 'Quiz time has expired', 'redirect' => "quiz_results.php?attempt_id={$attempt_id}"]));
}

// Get quiz questions
$questions = db()->fetchAll(
    "SELECT * FROM questions WHERE quiz_id = :quiz_id",
    ['quiz_id' => $attempt['quiz_id']]
);

// Process and save responses
$saved_count = 0;

foreach ($questions as $question) {
    $question_id = $question['question_id'];
    
    // Handle different question types
    if ($question['question_type'] == 'multiple_choice' || $question['question_type'] == 'true_false') {
        if (isset($_POST["question_{$question_id}"])) {
            $selected_option_id = $_POST["question_{$question_id}"];
            
            // Check if this response already exists
            $existing_response = db()->fetchOne(
                "SELECT * FROM student_responses 
                 WHERE attempt_id = :attempt_id AND question_id = :question_id",
                ['attempt_id' => $attempt_id, 'question_id' => $question_id]
            );
            
            if ($existing_response) {
                // Update existing response
                db()->query(
                    "UPDATE student_responses 
                     SET selected_option_id = :selected_option_id 
                     WHERE response_id = :response_id",
                    [
                        'selected_option_id' => $selected_option_id,
                        'response_id' => $existing_response['response_id']
                    ]
                );
            } else {
                // Create new response
                db()->query(
                    "INSERT INTO student_responses 
                     (attempt_id, question_id, selected_option_id) 
                     VALUES (:attempt_id, :question_id, :selected_option_id)",
                    [
                        'attempt_id' => $attempt_id,
                        'question_id' => $question_id,
                        'selected_option_id' => $selected_option_id
                    ]
                );
            }
            
            $saved_count++;
        }
    } else if ($question['question_type'] == 'fill_blank' || $question['question_type'] == 'essay') {
        if (isset($_POST["question_{$question_id}_text"])) {
            $text_response = $_POST["question_{$question_id}_text"];
            
            // Check if this response already exists
            $existing_response = db()->fetchOne(
                "SELECT * FROM student_responses 
                 WHERE attempt_id = :attempt_id AND question_id = :question_id",
                ['attempt_id' => $attempt_id, 'question_id' => $question_id]
            );
            
            if ($existing_response) {
                // Update existing response
                db()->query(
                    "UPDATE student_responses 
                     SET text_response = :text_response 
                     WHERE response_id = :response_id",
                    [
                        'text_response' => $text_response,
                        'response_id' => $existing_response['response_id']
                    ]
                );
            } else {
                // Create new response
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
            
            $saved_count++;
        }
    }
    // Matching question types would require more complex handling
}

// Return success response
echo json_encode([
    'success' => true, 
    'saved_count' => $saved_count,
    'timestamp' => date('Y-m-d H:i:s'),
    'time_remaining' => $time_limit_seconds - $elapsed_seconds
]);





?>