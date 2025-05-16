<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'You do not have permission to access this page');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$teacher_id = $user['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken($_POST['csrf']);
    $title = sanitize($_POST['title']);
    $level = sanitize($_POST['level']);
    $status = 'active'; // Default to active status
    
    // Insert the course with the creator_id as teacher_id
    executeNonQuery("INSERT INTO courses (title, level, creator_id, status, created_at) VALUES (?, ?, ?, ?, NOW())", 
        [$title, $level, $teacher_id, $status]);

    setFlashMessage('success', 'Course created!');
    redirect('courses.php');
}

// Get courses created by current teacher or shared with them
$my_courses_sql = "
SELECT DISTINCT c.* 
FROM courses c
LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
WHERE 
    c.creator_id = :teacher_id 
    OR ct.teacher_id = :teacher_id 
    OR cs.shared_with_id = :teacher_id
";

$my_courses = executeQueryAll($my_courses_sql, ['teacher_id' => $teacher_id]);

// Set up search and filtering for all courses
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$level_filter = isset($_GET['level']) ? sanitize($_GET['level']) : '';

// Build the query for all active courses except those created by the current teacher
$all_courses_sql = "
SELECT c.*, u.first_name, u.last_name 
FROM courses c
JOIN users u ON c.creator_id = u.user_id
WHERE c.status = 'active' AND c.creator_id != :teacher_id
";

$params = ['teacher_id' => $teacher_id];

// Add search condition if search term is provided
if (!empty($search)) {
    $all_courses_sql .= " AND (c.course_code LIKE :search OR c.title LIKE :search OR c.description LIKE :search)";
    $params['search'] = "%$search%";
}

// Add level filter if selected
if (!empty($level_filter)) {
    $all_courses_sql .= " AND c.level = :level";
    $params['level'] = $level_filter;
}

$all_courses_sql .= " ORDER BY c.created_at DESC";
$all_courses = executeQueryAll($all_courses_sql, $params);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h2></h2>
                            <div class="btn-toolbar mb-2 mb-md-0">
                                <a href="createCourse.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Create a New Course
                                </a>
                            </div>
                        </div>

                        <!-- My Courses Section -->
                        <h3 class="mt-4">Courses You Have Created</h3>
                        <?php if (count($my_courses) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Level</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($my_courses as $course): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($course['title']) ?></td>
                                                <td>
                                                    <span class="badge badge-info"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($course['status'] === 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= formatDate($course['created_at']) ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="viewCourse.php?id=<?= $course['course_id'] ?>" class="btn btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="editCourse.php?id=<?= $course['course_id'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You haven't created any courses yet.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- All Courses Section -->
                    <div class="card-body">
                        <h3 class="mt-2">All Courses</h3>
                        
                        <!-- Search and Filter Form -->
                        <form method="GET" action="" class="mb-4">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search courses..." name="search" value="<?= htmlspecialchars($search) ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <select name="level" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Levels</option>
                                        <option value="beginner" <?= $level_filter === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                        <option value="elementary" <?= $level_filter === 'elementary' ? 'selected' : '' ?>>Elementary</option>
                                        <option value="intermediate" <?= $level_filter === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                        <option value="upper-intermediate" <?= $level_filter === 'upper-intermediate' ? 'selected' : '' ?>>Upper Intermediate</option>
                                        <option value="advanced" <?= $level_filter === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                        <option value="proficiency" <?= $level_filter === 'proficiency' ? 'selected' : '' ?>>Proficiency</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>

                        <?php if (count($all_courses) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Title</th>
                                            <th>Level</th>
                                            <th>Created By</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_courses as $course): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                                <td><?= htmlspecialchars($course['title']) ?></td>
                                                <td>
                                                    <span class="badge badge-info"><?= ucfirst(htmlspecialchars($course['level'])) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($course['first_name'] . ' ' . $course['last_name']) ?></td>
                                                <td><?= formatDate($course['created_at']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No courses found matching your criteria.
                            </div>
                        <?php endif; ?>
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