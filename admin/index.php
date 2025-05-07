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

// Get system statistics
$stats = [
    'total_users' => db()->fetch("SELECT COUNT(*) as count FROM users", [])['count'],
    'total_students' => db()->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'student'", [])['count'],
    'total_teachers' => db()->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'", [])['count'],
    'total_courses' => db()->fetch("SELECT COUNT(*) as count FROM courses", [])['count'],
    'active_courses' => db()->fetch("SELECT COUNT(*) as count FROM courses WHERE status = 'active'", [])['count'],
    'total_enrollments' => db()->fetch("SELECT COUNT(*) as count FROM enrollments", [])['count'],
    'total_exams' => db()->fetch("SELECT COUNT(*) as count FROM exams", [])['count'],
    'total_resources' => db()->fetch("SELECT COUNT(*) as count FROM library_resources", [])['count']
];

// Get recent activities
$recentActivities = db()->fetchAll(
    "SELECT al.*, u.username, u.first_name, u.last_name 
     FROM activity_logs al 
     LEFT JOIN users u ON al.user_id = u.user_id 
     ORDER BY al.created_at DESC LIMIT 10"
);

// Get new users
$newUsers = db()->fetchAll(
    "SELECT user_id, username, first_name, last_name, email, role, date_registered 
     FROM users 
     ORDER BY date_registered DESC LIMIT 5"
);

// Get upcoming exams
$upcomingExams = db()->fetchAll(
    "SELECT e.*, c.title as course_title, c.course_code 
     FROM exams e 
     JOIN courses c ON e.course_id = c.course_id 
     WHERE e.exam_date > NOW() 
     ORDER BY e.exam_date ASC LIMIT 5"
);

// Get popular courses
$popularCourses = db()->fetchAll(
    "SELECT c.*, 
  COUNT(e.enrollment_id) AS enrollment_count,
  u.first_name,
  u.last_name
FROM courses c
LEFT JOIN enrollments e ON c.course_id = e.course_id
LEFT JOIN course_teachers ct ON c.course_id = ct.course_id
LEFT JOIN users u ON ct.teacher_id = u.user_id
GROUP BY c.course_id
ORDER BY enrollment_count DESC
LIMIT 5;
"
);

// Get notifications
$notifications = db()->fetchAll(
    "SELECT * FROM notifications 
     WHERE user_id = :user_id AND is_read = 0 
     ORDER BY created_at DESC LIMIT 5",
    ['user_id' => $user['user_id']]
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
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
                    <h1 class="h2">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                                <li><a class="dropdown-item" href="#">Export as Excel</a></li>
                                <li><a class="dropdown-item" href="#">Export as CSV</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_users']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Courses</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['active_courses']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Enrollments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_enrollments']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Library Resources</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_resources']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-book-reader fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Activities -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                                <a href="../admin/reports/activity-logs.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Description</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentActivities as $activity): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($activity['user_id']): ?>
                                                            <?php echo $activity['first_name'] . ' ' . $activity['last_name']; ?>
                                                        <?php else: ?>
                                                            System
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo ucfirst($activity['action']); ?></td>
                                                    <td><?php echo $activity['description']; ?></td>
                                                    <td><?php echo timeElapsed($activity['created_at']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($recentActivities)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No recent activities</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- New Users -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">New Users</h6>
                                <a href="../admin/users/index.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($newUsers as $user): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo ucfirst($user['role']); ?></small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill" title="Registered <?php echo timeElapsed($user['date_registered']); ?>">
                                                New
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($newUsers)): ?>
                                        <li class="list-group-item text-center">No new users</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Popular Courses -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Popular Courses</h6>
                                <a href="../admin/courses/index.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Teacher</th>
                                                <th>Level</th>
                                                <th>Enrollments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($popularCourses as $course): ?>
                                                <tr>
                                                    <td>
                                                        <a href="../admin/courses/view.php?id=<?php echo $course['course_id']; ?>">
                                                            <?php echo $course['title']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $course['first_name'] . ' ' . $course['last_name']; ?></td>
                                                    <td><?php echo ucfirst($course['level']); ?></td>
                                                    <td><?php echo $course['enrollment_count']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($popularCourses)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No courses available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Exams -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Upcoming Exams</h6>
                                <a href="../admin/exams/index.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Exam</th>
                                                <th>Course</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcomingExams as $exam): ?>
                                                <tr>
                                                    <td>
                                                        <a href="../admin/exams/view.php?id=<?php echo $exam['exam_id']; ?>">
                                                            <?php echo $exam['title']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $exam['course_title']; ?></td>
                                                    <td><?php echo formatDate($exam['exam_date'], 'M d, Y H:i'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $exam['status'] == 'scheduled' ? 'info' : 'warning'; ?>">
                                                            <?php echo ucfirst($exam['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($upcomingExams)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No upcoming exams</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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