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
$course_id = (int)$_GET['id'];
// Get course information and verify enrollment
$course = db()->fetchOne(
    "SELECT c.*, u.first_name, u.last_name, e.status as enrollment_status, e.enrollment_date
     FROM courses c
     JOIN course_teachers ct ON c.course_id = ct.course_id
     JOIN users u ON ct.teacher_id = u.user_id
     JOIN enrollments e ON c.course_id = e.course_id
     WHERE c.course_id = :course_id AND e.student_id = :student_id AND e.status != 'dropped'",
    ['course_id' => $course_id, 'student_id' => $student_id]
);
if (!$course) {
    setFlashMessage('error', 'Course not found or you are not enrolled in this course');
    redirect('index.php');
}

// Get course lessons with completion status
$lessons = db()->fetchAll(
    "SELECT l.*, 
            (SELECT COUNT(*) FROM lesson_completion WHERE lesson_id = l.lesson_id AND student_id = :student_id) as is_completed
     FROM lessons l
     WHERE l.course_id = :course_id
     ORDER BY l.sequence_order ASC",
    ['course_id' => $course_id, 'student_id' => $student_id]
);

// Count completed lessons
$completed_lessons = 0;
$total_lessons = count($lessons);
foreach ($lessons as $lesson) {
    if ($lesson['is_completed'] > 0) {
        $completed_lessons++;
    }
}

// Get assignments with submission status
$assignments = db()->fetchAll(
    "SELECT a.*, 
            (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.assignment_id AND student_id = :student_id AND (status = 'submitted' OR status = 'graded')) as is_submitted
     FROM assignments a
     WHERE a.course_id = :course_id
     ORDER BY a.sequence_order ASC",
    ['course_id' => $course_id, 'student_id' => $student_id]
);

// Count completed assignments
$completed_assignments = 0;
$total_assignments = count($assignments);
foreach ($assignments as $assignment) {
    if ($assignment['is_submitted'] > 0) {
        $completed_assignments++;
    }
}

// Get quizzes with attempt status
$quizzes = db()->fetchAll(
    "SELECT q.*,
            (SELECT MAX(score) FROM quiz_attempts WHERE quiz_id = q.quiz_id AND student_id = :student_id_1 AND status = 'completed') as best_score,
            (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.quiz_id AND student_id = :student_id_2 AND status = 'completed') as attempts_made
     FROM quizzes q
     WHERE q.course_id = :course_id
     ORDER BY q.sequence_order ASC",
    ['course_id' => $course_id, 'student_id_1' => $student_id, 'student_id_2' => $student_id]
);

// Count completed quizzes (considering passing score)
$completed_quizzes = 0;
$total_quizzes = count($quizzes);
foreach ($quizzes as $quiz) {
    if ($quiz['attempts_made'] > 0 && $quiz['best_score'] >= $quiz['passing_score']) {
        $completed_quizzes++;
    }
}

// Get exams with results
$exams = db()->fetchAll(
    "SELECT e.*,
            (SELECT COUNT(*) FROM exam_results WHERE exam_id = e.exam_id AND student_id = :student_id AND status = 'pass') as is_passed
     FROM exams e
     WHERE e.course_id = :course_id
     ORDER BY e.sequence_order ASC",
    ['course_id' => $course_id, 'student_id' => $student_id]
);

// Count completed exams
$completed_exams = 0;
$total_exams = count($exams);
foreach ($exams as $exam) {
    if ($exam['is_passed'] > 0) {
        $completed_exams++;
    }
}

// Calculate overall course progress
$total_items = $total_lessons + $total_assignments + $total_quizzes + $total_exams;
$completed_items = $completed_lessons + $completed_assignments + $completed_quizzes + $completed_exams;

$progress_percentage = ($total_items > 0) ? round(($completed_items / $total_items) * 100, 2) : 0;

// Update student_progress table
db()->query(
    "INSERT INTO student_progress 
        (student_id, course_id, progress_percentage, lessons_completed, assignments_completed, quizzes_completed, exams_completed, last_accessed) 
     VALUES 
        (:student_id, :course_id, :progress, :lessons, :assignments, :quizzes, :exams, NOW())
     ON DUPLICATE KEY UPDATE
        progress_percentage = :progress,
        lessons_completed = :lessons,
        assignments_completed = :assignments,
        quizzes_completed = :quizzes,
        exams_completed = :exams,
        last_accessed = NOW()",
    [
        'student_id' => $student_id,
        'course_id' => $course_id,
        'progress' => $progress_percentage,
        'lessons' => $completed_lessons,
        'assignments' => $completed_assignments,
        'quizzes' => $completed_quizzes,
        'exams' => $completed_exams
    ]
);

// Get upcoming assignments for this course
$upcomingAssignments = db()->fetchAll(
    "SELECT a.*
     FROM assignments a
     JOIN lessons l ON a.course_id = l.course_id
     LEFT JOIN submissions s ON a.assignment_id = s.assignment_id AND s.student_id = :student_id
     WHERE a.course_id = :course_id
     AND (s.submission_id IS NULL OR s.status = 'resubmitted')
     AND a.due_date > NOW()
     ORDER BY a.due_date ASC
     LIMIT 5",
    ['student_id' => $student_id, 'course_id' => $course_id]
);

// Get upcoming quizzes for this course
$upcomingQuizzes = db()->fetchAll(
    "SELECT q.*
     FROM quizzes q
     LEFT JOIN (
         SELECT quiz_id, COUNT(attempt_id) as attempts, MAX(score) as max_score
         FROM quiz_attempts
         WHERE student_id = :student_id_sub AND status = 'completed'
         GROUP BY quiz_id
     ) qa ON q.quiz_id = qa.quiz_id
     WHERE q.course_id = :course_id
     AND (qa.attempts IS NULL OR qa.attempts < q.attempts_allowed OR qa.max_score < q.passing_score)
     AND q.status = 'active'
     ORDER BY q.sequence_order ASC
     LIMIT 5",
    ['student_id_sub' => $student_id, 'course_id' => $course_id]
);

// Get upcoming exams
$upcomingExams = db()->fetchAll(
    "SELECT e.*
     FROM exams e
     LEFT JOIN exam_results er ON e.exam_id = er.exam_id AND er.student_id = :student_id
     WHERE e.course_id = :course_id
     AND (er.result_id IS NULL OR er.status = 'fail')
     AND e.exam_date > NOW() 
     AND e.status = 'active'
     ORDER BY e.exam_date ASC
     LIMIT 5",
    ['student_id' => $student_id, 'course_id' => $course_id]
);

// Get recent progress in this course
$recentProgress = db()->fetchAll(
    "SELECT 'lesson' as activity_type, lc.completed_at as activity_date, l.title as title, l.lesson_id as id
     FROM lesson_completion lc
     JOIN lessons l ON lc.lesson_id = l.lesson_id
     WHERE lc.student_id = :student_id_1 AND l.course_id = :course_id_1
     
     UNION ALL
     
     SELECT 'assignment' as activity_type, s.submission_date as activity_date, a.title as title, a.assignment_id as id
     FROM submissions s
     JOIN assignments a ON s.assignment_id = a.assignment_id
     WHERE s.student_id = :student_id_2 AND a.course_id = :course_id_2 AND (s.status = 'submitted' OR s.status = 'graded')
     
     UNION ALL
     
     SELECT 'quiz' as activity_type, qa.end_time as activity_date, q.title as title, q.quiz_id as id
     FROM quiz_attempts qa
     JOIN quizzes q ON qa.quiz_id = q.quiz_id
     WHERE qa.student_id = :student_id_3 AND q.course_id = :course_id_3 AND qa.status = 'completed'
     
     UNION ALL
     
     SELECT 'exam' as activity_type, er.evaluated_at as activity_date, e.title as title, e.exam_id as id
     FROM exam_results er
     JOIN exams e ON er.exam_id = e.exam_id
     WHERE er.student_id = :student_id_4 AND e.course_id = :course_id_4
     
     ORDER BY activity_date DESC
     LIMIT 5",
    [
        'student_id_1' => $student_id, 'course_id_1' => $course_id,
        'student_id_2' => $student_id, 'course_id_2' => $course_id,
        'student_id_3' => $student_id, 'course_id_3' => $course_id,
        'student_id_4' => $student_id, 'course_id_4' => $course_id
    ]
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="courses.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> My Courses
                        </a>
                    </div>
                </div>

                <?php displayFlashMessage(); ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Course Details</h5>
                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['course_code']); ?></p>
                        <p><strong>Teacher:</strong> <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?></p>
                        <p><strong>Level:</strong> <?php echo ucfirst($course['level']); ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($course['enrollment_status']); ?></p>
                        <p><strong>Enrolled On:</strong> <?php echo formatDate($course['enrollment_date']); ?></p>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        <p><strong>Course Completion:</strong> <?php echo $progress_percentage; ?>%</p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $courseCompletion; ?>%;" aria-valuenow="<?php echo $courseCompletion; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <!-- Lessons -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Lessons</h5>
                        <?php if (count($lessons) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($lessons as $lessons): ?>
                                    <a href="lessons.php?id=<?php echo $lessons['lesson_id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($lessons['title']); ?></strong>
                                        </div>
                                        <span class="badge bg-primary rounded-pill"><?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?> Lessons</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No lessons available yet for this course.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Assignments -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Assignments</h5>
                        <?php if (count($upcomingAssignments) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($upcomingAssignments as $assignment): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($assignment['title']); ?></strong><br>
                                            <small class="text-muted">Lesson: <?php echo htmlspecialchars($assignment['lesson_title']); ?></small><br>
                                            <small class="text-muted">Due: <?php echo formatDateTime($assignment['due_date']); ?></small>
                                        </div>
                                        <a href="../assignments.php?id=<?php echo $assignment['assignment_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No upcoming assignments.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Quizzes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Quizzes</h5>
                        <?php if (count($upcomingQuizzes) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($upcomingQuizzes as $quiz): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($quiz['title']); ?></strong><br>
                                        </div>
                                        <a href="../quizzes.php?id=<?php echo $quiz['quiz_id']; ?>" class="btn btn-sm btn-warning">Take Quiz</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No upcoming quizzes.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Progress -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Course Progress</h5>
        <div class="progress mb-3">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" 
                 aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $progress_percentage; ?>%
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <span class="badge bg-primary rounded-pill"><?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?> Lessons</span>
            <span class="badge bg-success rounded-pill"><?php echo $completed_assignments; ?>/<?php echo $total_assignments; ?> Assignments</span>
            <span class="badge bg-warning rounded-pill"><?php echo $completed_quizzes; ?>/<?php echo $total_quizzes; ?> Quizzes</span>
            <span class="badge bg-danger rounded-pill"><?php echo $completed_exams; ?>/<?php echo $total_exams; ?> Exams</span>
        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
