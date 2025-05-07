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

// Get enrolled courses with filters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$level = $_GET['level'] ?? 'all';
$params = ['student_id' => $student_id];

// Modified SQL query to correctly join tables based on the schema
$sql = "SELECT 
            c.*, 
            e.enrollment_date, 
            e.status as enrollment_status, 
            e.teacher_id,
            u.first_name, 
            u.last_name,
            COALESCE(sp.progress_percentage, 0) as completion_percentage 
        FROM 
            enrollments e
            JOIN courses c ON e.course_id = c.course_id
            JOIN users u ON e.teacher_id = u.user_id
            LEFT JOIN student_progress sp ON (sp.student_id = e.student_id AND sp.course_id = e.course_id)
        WHERE 
            e.student_id = :student_id";

if (!empty($search)) {
    $sql .= " AND (c.title LIKE :search_title OR c.description LIKE :search_description OR c.course_code LIKE :search_code)";
    $params['search_title'] = "%$search%";
    $params['search_description'] = "%$search%";
    $params['search_code'] = "%$search%";
}

if ($status !== 'all') {
    $sql .= " AND e.status = :status";
    $params['status'] = $status;
}

if ($level !== 'all') {
    $sql .= " AND c.level = :level";
    $params['level'] = $level;
}

$sql .= " ORDER BY e.enrollment_date DESC";
$enrolledCourses = db()->fetchAll($sql, $params);

// Get available levels for filter
$levels = db()->fetchAll("SELECT DISTINCT level FROM courses ORDER BY FIELD(level, 'beginner', 'elementary', 'intermediate', 'upper-intermediate', 'advanced', 'proficiency')");

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$total = count($enrolledCourses);
$totalPages = ceil($total / $perPage);
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;
$paginatedCourses = array_slice($enrolledCourses, $offset, $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - <?php echo APP_NAME; ?></title>
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
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Courses</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="enroll.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Enroll in New Course
                        </a>
                    </div>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by title, code...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="all" <?php if($status == 'all') echo 'selected'; ?>>All Statuses</option>
                                    <option value="ongoing" <?php if($status == 'ongoing') echo 'selected'; ?>>Ongoing</option>
                                    <option value="completed" <?php if($status == 'completed') echo 'selected'; ?>>Completed</option>
                                    <option value="pending" <?php if($status == 'pending') echo 'selected'; ?>>Pending</option>
                                    <option value="dropped" <?php if($status == 'dropped') echo 'selected'; ?>>Dropped</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-control" id="level" name="level">
                                    <option value="all" <?php if($level == 'all') echo 'selected'; ?>>All Levels</option>
                                    <?php foreach($levels as $lvl): ?>
                                        <option value="<?php echo $lvl['level']; ?>" <?php if($level == $lvl['level']) echo 'selected'; ?>>
                                            <?php echo ucfirst($lvl['level']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Course List -->
                <div class="card">
                    <div class="card-body">
                        <?php if (count($paginatedCourses) > 0): ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                                <?php foreach ($paginatedCourses as $course): ?>
                                    <?php 
                                    // Use completion percentage from query if available, otherwise calculate
                                    $completion = isset($course['completion_percentage']) ? 
                                        $course['completion_percentage'] : 
                                        getCourseCompletion($student_id, $course['course_id']); 
                                    ?>
                                    <div class="col mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <span class="badge badge-<?php echo getStatusBadgeClass($course['enrollment_status']); ?> float-right">
                                                    <?php echo ucfirst($course['enrollment_status']); ?>
                                                </span>
                                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($course['title']); ?></h5>
                                                <small class="text-muted"><?php echo htmlspecialchars($course['course_code']); ?></small>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>
                                                    <?php if (strlen($course['description']) > 100): ?>...</<?php endif; ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span>
                                                        <i class="fas fa-user-tie"></i> 
                                                        <?php echo htmlspecialchars($course['first_name'] . ' ' . $course['last_name']); ?>
                                                    </span>
                                                    <span class="badge badge-info">
                                                        <?php echo ucfirst($course['level']); ?>
                                                    </span>
                                                </div>
                                                <div class="progress mb-2" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?php echo round($completion); ?>%;" 
                                                         aria-valuenow="<?php echo round($completion); ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-muted"><?php echo round($completion); ?>% complete</small>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-grid">
                                                    <a href="viewCourses.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-block">
                                                        Open Course
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Course pagination" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&level=<?php echo $level; ?>" tabindex="-1">Previous</a>
                                        </li>
                                        
                                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&level=<?php echo $level; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&level=<?php echo $level; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="alert alert-info">
                                No courses found matching your criteria. 
                                <a href="enroll.php" class="alert-link">Enroll in new courses</a> to start learning!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    // Add any custom JS here
    $(document).ready(function() {
        // Initialize any Bootstrap components if needed
    });
    </script>
</body>
</html>