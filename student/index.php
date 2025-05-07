<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a student
if (!Auth::isLoggedIn() || !Auth::hasRole('student')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../login.php');
}

$user = Auth::getCurrentUser();
$student_id = $user['user_id'];

// Get enrolled courses
$enrolledCourses = db()->fetchAll(
    "SELECT c.*, e.enrollment_date, e.status as enrollment_status, 
        u.first_name, u.last_name
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        JOIN users u ON e.teacher_id = u.user_id
        WHERE e.student_id = :student_id AND e.status != 'dropped'
        ORDER BY e.enrollment_date DESC",
    ['student_id' => $student_id]
);



// Get upcoming assignments
$upcomingAssignments = db()->fetchAll(
    "SELECT a.*, c.title as course_title, c.course_id
     FROM assignments a
     JOIN courses c ON a.course_id = c.course_id
     JOIN enrollments e ON c.course_id = e.course_id
     LEFT JOIN submissions s ON a.assignment_id = s.assignment_id AND s.student_id = :submission_student_id
     WHERE e.student_id = :enrollment_student_id
       AND e.status = 'active'
       AND (s.submission_id IS NULL OR s.status = 'resubmitted')
       AND a.due_date > NOW()
     ORDER BY a.due_date ASC
     LIMIT 5",
    [
        'submission_student_id' => $student_id,
        'enrollment_student_id' => $student_id
    ]
);



// Get upcoming quizzes
$upcomingQuizzes = db()->fetchAll(
    "SELECT q.*, c.title as course_title, c.course_id
     FROM quizzes q
     JOIN courses c ON q.course_id = c.course_id
     JOIN enrollments e ON c.course_id = e.course_id
     LEFT JOIN (
         SELECT quiz_id, COUNT(attempt_id) as attempts, MAX(score) as max_score
         FROM quiz_attempts
         WHERE student_id = :attempt_student_id
         GROUP BY quiz_id
     ) qa ON q.quiz_id = qa.quiz_id
     WHERE e.student_id = :enrollment_student_id
       AND e.status = 'ongoing'  -- Changed from 'active' to match enrollments.status enum
       AND (qa.attempts IS NULL OR qa.attempts < q.attempts_allowed)
       AND q.status = 'active'
     ORDER BY q.sequence_order ASC
     LIMIT 5",
    [
        'attempt_student_id' => $student_id,
        'enrollment_student_id' => $student_id
    ]
);


// Get recent progress
$recentProgress = db()->fetchAll(
    "SELECT lc.*, l.title as lesson_title, c.title as course_title, c.course_id
     FROM lesson_completion lc
     JOIN lessons l ON lc.lesson_id = l.lesson_id
     JOIN courses c ON l.course_id = c.course_id
     WHERE lc.student_id = :student_id
     ORDER BY lc.completed_at DESC
     LIMIT 5",
    ['student_id' => $student_id]
);


// Get notifications
$notifications = db()->fetchAll(
    "SELECT * FROM notifications 
     WHERE user_id = :user_id AND is_read = 0 
     ORDER BY created_at DESC LIMIT 5",
    ['user_id' => $student_id]
);


// Overall progress across all courses
$overallProgress = 0;
$totalCourses = count($enrolledCourses);
if ($totalCourses > 0) {
    $totalProgress = 0;
    foreach ($enrolledCourses as $course) {
        $completion = getCourseCompletion($student_id, $course['course_id']);
        $totalProgress += $completion;
    }
    $overallProgress = $totalProgress / $totalCourses;
}


// Get upcoming exams
$upcomingExams = db()->fetchAll(
    "SELECT e.*, c.title as course_title, c.course_code
     FROM exams e
     JOIN courses c ON e.course_id = c.course_id
     JOIN enrollments en ON c.course_id = en.course_id
     LEFT JOIN exam_results er ON e.exam_id = er.exam_id AND er.student_id = :exam_student_id 
     WHERE en.student_id = :enrollment_student_id 
       AND en.status = 'active'
       AND e.exam_date > NOW() 
       AND e.status = 'scheduled'
       AND er.result_id IS NULL
     ORDER BY e.exam_date ASC
     LIMIT 5",
    [
        'exam_student_id' => $student_id, 
        'enrollment_student_id' => $student_id
    ]
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="public/assets/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="enroll.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Enroll in New Course
                        </a>
                    </div>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Welcome Message -->
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Welcome, <?php echo $user['first_name']; ?>!</h4>
                    <p>Here's your learning progress and upcoming activities. Keep up the good work!</p>
                </div>
                
                <!-- Overall Progress -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Overall Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo round($overallProgress); ?>%;" 
                                 aria-valuenow="<?php echo round($overallProgress); ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo round($overallProgress); ?>%
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Average completion across <?php echo $totalCourses; ?> enrolled courses</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Enrolled Courses -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">My Courses</h5>
                                <a href="courses.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($enrolledCourses)): ?>
                                    <div class="list-group">
                                        <?php foreach ($enrolledCourses as $course): ?>
                                            <?php $completion = getCourseCompletion($student_id, $course['course_id']); ?>
                                            <a href="viewCourses.php?id=<?php echo $course['course_id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1"><?php echo $course['title']; ?></h5>
                                                    <small class="text-muted"><?php echo ucfirst($course['level']); ?></small>
                                                </div>
                                                <p class="mb-1">Teacher: <?php echo $course['first_name'] . ' ' . $course['last_name']; ?></p>
                                                <div class="progress mt-2" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?php echo round($completion); ?>%;" 
                                                         aria-valuenow="<?php echo round($completion); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="text-muted"><?php echo round($completion); ?>% complete</small>
                                                    <span class="badge bg-<?php echo getStatusBadgeClass($course['enrollment_status']); ?>">
                                                        <?php echo ucfirst($course['enrollment_status']); ?>
                                                    </span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        You are not enrolled in any courses yet. 
                                        <a href="enroll.php" class="alert-link">Enroll now</a> to start learning!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Activity</h5>
                                <a href="progress.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recentProgress)): ?>
                                    <div class="list-group">
                                        <?php foreach ($recentProgress as $progress): ?>
                                            <a href="lessons/view.php?id=<?php echo $progress['lesson_id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1"><?php echo $progress['lesson_title']; ?></h5>
                                                    <small><?php echo timeElapsed($progress['last_accessed']); ?></small>
                                                </div>
                                                <p class="mb-1">Course: <?php echo $progress['course_title']; ?></p>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Progress: <?php echo round($progress['progress_percentage']); ?>%</small>
                                                    <span class="badge bg-<?php echo getProgressBadgeClass($progress['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $progress['status'])); ?>
                                                    </span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        You haven't started any lessons yet. 
                                        <?php if (!empty($enrolledCourses)): ?>
                                            <a href="courses.php?id=<?php echo $enrolledCourses[0]['course_id']; ?>" class="alert-link">
                                                Start learning now!
                                            </a>
                                        <?php else: ?>
                                            Enroll in a course to begin your learning journey.
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Upcoming Assignments -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Upcoming Assignments</h5>
                                <a href="assignments.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($upcomingAssignments)): ?>
                                    <div class="list-group">
                                        <?php foreach ($upcomingAssignments as $assignment): ?>
                                            <a href="assignments.php?id=<?php echo $assignment['assignment_id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1"><?php echo $assignment['title']; ?></h5>
                                                    <small class="text-<?php echo isUrgent($assignment['due_date']) ? 'danger' : 'muted'; ?>">
                                                        Due: <?php echo formatDate($assignment['due_date'], 'M d, Y'); ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1">
                                                    <span class="badge bg-info"><?php echo $assignment['course_title']; ?></span>
                                                    <span class="badge bg-secondary"><?php echo $assignment['lesson_title']; ?></span>
                                                </p>
                                                <small>Points: <?php echo $assignment['max_points']; ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-success">
                                        You don't have any pending assignments! Great job keeping up with your work.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Quizzes and Exams -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Upcoming Tests</h5>
                                <div>
                                    <a href="quizzes.php" class="btn btn-sm btn-outline-primary">Quizzes</a>
                                    <a href="exams.php" class="btn btn-sm btn-outline-primary">Exams</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="testsTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" 
                                                type="button" role="tab" aria-controls="quizzes" aria-selected="true">Quizzes</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" 
                                                type="button" role="tab" aria-controls="exams" aria-selected="false">Exams</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="testsTabContent">
                                    <div class="tab-pane fade show active" id="quizzes" role="tabpanel" aria-labelledby="quizzes-tab">
                                        <?php if (!empty($upcomingQuizzes)): ?>
                                            <div class="list-group mt-3">
                                                <?php foreach ($upcomingQuizzes as $quiz): ?>
                                                    <a href="quizzes.php?id=<?php echo $quiz['quiz_id']; ?>" class="list-group-item list-group-item-action">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1"><?php echo $quiz['title']; ?></h5>
                                                            <small>
                                                                <?php echo $quiz['time_limit']; ?> min
                                                            </small>
                                                        </div>
                                                        <p class="mb-1">
                                                            <span class="badge bg-info"><?php echo $quiz['course_title']; ?></span>
                                                        <small>Passing Score: <?php echo $quiz['passing_score']; ?>%</small>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success mt-3">
                                                You don't have any upcoming quizzes at the moment.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="exams" role="tabpanel" aria-labelledby="exams-tab">
                                        <?php if (!empty($upcomingExams)): ?>
                                            <div class="list-group mt-3">
                                                <?php foreach ($upcomingExams as $exam): ?>
                                                    <a href="exams.php?id=<?php echo $exam['exam_id']; ?>" class="list-group-item list-group-item-action">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h5 class="mb-1"><?php echo $exam['title']; ?></h5>
                                                            <small class="text-<?php echo isUrgent($exam['exam_date'], 3) ? 'danger' : 'muted'; ?>">
                                                                <?php echo formatDate($exam['exam_date'], 'M d, Y H:i'); ?>
                                                            </small>
                                                        </div>
                                                        <p class="mb-1">
                                                            <span class="badge bg-info"><?php echo $exam['course_title']; ?></span>
                                                            <span class="badge bg-warning text-dark">Duration: <?php echo $exam['duration']; ?> min</span>
                                                        </p>
                                                        <small>Total marks: <?php echo $exam['total_marks']; ?> (Pass: <?php echo $exam['passing_marks']; ?>)</small>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success mt-3">
                                                You don't have any upcoming exams scheduled.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications -->
                <?php if (!empty($notifications)): ?>
                    <div class="card mb-4">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Notifications</h5>
                                <button class="btn btn-sm btn-outline-secondary mark-all-read">
                                    Mark All as Read
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php foreach ($notifications as $notification): ?>
                                        <a href="#" class="list-group-item list-group-item-action notification-item" 
                                           data-id="<?php echo $notification['notification_id']; ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo $notification['title']; ?></h6>
                                                <small><?php echo timeElapsed($notification['created_at']); ?></small>
                                            </div>
                                            <p class="mb-1"><?php echo $notification['message']; ?></p>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Mark notification as read when clicked
        $('.notification-item').click(function(e) {
            e.preventDefault();
            var notificationId = $(this).data('id');
            var $item = $(this);
            
            $.ajax({
                url: '../api/notifications/mark_read.php',
                method: 'POST',
                data: { notification_id: notificationId },
                success: function(response) {
                    if (response.success) {
                        $item.addClass('list-group-item-light');
                        $item.find('h6').css('font-weight', 'normal');
                    }
                }
            });
        });
        
        // Mark all notifications as read
        $('.mark-all-read').click(function() {
            $.ajax({
                url: '../api/notifications/mark_all_read.php',
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        $('.notification-item').each(function() {
                            $(this).addClass('list-group-item-light');
                            $(this).find('h6').css('font-weight', 'normal');
                        });
                    }
                }
            });
        });
        
        // Function to check if due date is urgent (within 3 days)
        function isUrgent(dueDate) {
            const now = new Date();
            const due = new Date(dueDate);
            const diffTime = due - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
            return diffDays <= 3;
        }
    });
    </script>
</body>
</html>