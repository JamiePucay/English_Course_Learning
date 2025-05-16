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

if (isset($_GET['id'])) {
    $course_id = (int) $_GET['id'];

    // Allows the teacher to edit the course if they are the creator, assigned teacher, or it's shared with them
    $sql = "SELECT c.* FROM courses c
        LEFT JOIN course_teachers ct ON ct.course_id = c.course_id
        LEFT JOIN course_sharing cs ON cs.course_id = c.course_id
        WHERE c.course_id = :course_id
        AND (
            c.creator_id = :teacher_id
            OR ct.teacher_id = :teacher_id
            OR cs.shared_with_id = :teacher_id
        )";

    $course = executeQuerySingle($sql, [
        'course_id' => $course_id,
        'teacher_id' => $teacher_id
    ]);

    if (!$course) {
        setFlashMessage('error', 'Course not found or you do not have permission to edit this course.');
        redirect('courses.php');
    }

    // Check sharing permissions for this course
    $sharing_sql = "SELECT * FROM course_sharing WHERE course_id = :course_id AND shared_with_id = :teacher_id";
    $sharing = executeQuerySingle($sharing_sql, [
        'course_id' => $course_id,
        'teacher_id' => $teacher_id
    ]);

    $permission = $sharing ? $sharing['permission'] : 'full_access'; // Default to full_access if course creator

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validateCSRFToken($_POST['csrf']);
        $title = sanitize($_POST['title']);
        $level = sanitize($_POST['level']);
        $status = sanitize($_POST['status']);
        $course_code = sanitize($_POST['course_code']);
        $description = sanitize($_POST['description']);
        $duration = (int)$_POST['duration'];
        
        // Update sharing permission if applicable
        if (isset($_POST['permission']) && $sharing) {
            $new_permission = sanitize($_POST['permission']);
            executeNonQuery("UPDATE course_sharing SET permission = ? WHERE sharing_id = ?", 
                [$new_permission, $sharing['sharing_id']]);
        }

        // Update the course
        executeNonQuery("UPDATE courses SET title = ?, level = ?, status = ?, course_code = ?, description = ?, duration = ? WHERE course_id = ?", 
            [$title, $level, $status, $course_code, $description, $duration, $course_id]);

        setFlashMessage('success', 'Course updated successfully!');
        redirect('viewCourse.php?id=' . $course_id);
    }
    
    // Get lessons
    $lessons_sql = "SELECT * FROM lessons WHERE course_id = :course_id ORDER BY sequence_order ASC";
    $lessons = executeQueryAll($lessons_sql, ['course_id' => $course_id]);
    
    // Get assignments
    $assignments_sql = "SELECT * FROM assignments WHERE course_id = :course_id ORDER BY sequence_order ASC";
    $assignments = executeQueryAll($assignments_sql, ['course_id' => $course_id]);
    
    // Get quizzes
    $quizzes_sql = "SELECT * FROM quizzes WHERE course_id = :course_id ORDER BY sequence_order ASC";
    $quizzes = executeQueryAll($quizzes_sql, ['course_id' => $course_id]);
    
    // Get exams
    $exams_sql = "SELECT * FROM exams WHERE course_id = :course_id ORDER BY sequence_order ASC";
    $exams = executeQueryAll($exams_sql, ['course_id' => $course_id]);
    
} else {
    setFlashMessage('error', 'Course ID is missing.');
    redirect('courses.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Course</h1>
                </div>
                
                
                <!-- Course Edit Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Course Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Main Information</h5>
                                    
                                    <div class="form-group">
                                        <label for="title">Course Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                            value="<?= htmlspecialchars($course['title']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="course_code">Course Code</label>
                                        <input type="text" class="form-control" id="course_code" name="course_code" 
                                            value="<?= htmlspecialchars($course['course_code']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" id="level" name="level" required>
                                            <option value="beginner" <?= $course['level'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                            <option value="elementary" <?= $course['level'] === 'elementary' ? 'selected' : '' ?>>Elementary</option>
                                            <option value="intermediate" <?= $course['level'] === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                            <option value="upper-intermediate" <?= $course['level'] === 'upper-intermediate' ? 'selected' : '' ?>>Upper-Intermediate</option>
                                            <option value="advanced" <?= $course['level'] === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                            <option value="proficiency" <?= $course['level'] === 'proficiency' ? 'selected' : '' ?>>Proficiency</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="active" <?= $course['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= $course['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <?php if ($sharing): ?>
                                    <div class="form-group">
                                        <label for="permission">Sharing Permission</label>
                                        <select class="form-control" id="permission" name="permission">
                                            <option value="view" <?= $permission === 'view' ? 'selected' : '' ?>>View Only</option>
                                            <option value="full_access" <?= $permission === 'full_access' ? 'selected' : '' ?>>Full Access</option>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Additional Information</h5>
                                    
                                    <div class="form-group">
                                        <label for="duration">Duration (weeks)</label>
                                        <input type="number" class="form-control" id="duration" name="duration" 
                                               value="<?= htmlspecialchars($course['duration'] ?? '') ?>" min="1">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <a href="viewCourse.php?id=<?= $course_id ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Manage Course Content -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Manage Course Content</h5>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="lessons-tab" data-toggle="tab" href="#lessons" role="tab">Lessons</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="assignments-tab" data-toggle="tab" href="#assignments" role="tab">Assignments</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="quizzes-tab" data-toggle="tab" href="#quizzes" role="tab">Quizzes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="exams-tab" data-toggle="tab" href="#exams" role="tab">Exams</a>
                            </li>
                        </ul>
                        
                        <!-- Tab content -->
                        <div class="tab-content p-3">
                            <!-- Lessons Tab -->
                            <div class="tab-pane fade show active" id="lessons" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Lessons</h5>
                                    <a href="createLesson.php?course_id=<?= $course_id ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Lesson
                                    </a>
                                </div>
                                
                                <?php if (count($lessons) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Title</th>
                                                <th>Duration (min)</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lessons as $lesson): ?>
                                            <tr>
                                                <td><?= $lesson['sequence_order'] ?></td>
                                                <td><?= htmlspecialchars($lesson['title']) ?></td>
                                                <td><?= $lesson['duration'] ? $lesson['duration'] . ' mins' : 'N/A' ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $lesson['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($lesson['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="editLesson.php?id=<?= $lesson['lesson_id'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="deleteLesson.php?id=<?= $lesson['lesson_id'] ?>" class="btn btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this lesson?');">
                                                            <i class="fas fa-trash"></i> Delete
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
                                    <i class="fas fa-info-circle"></i> No lessons have been created for this course yet.
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Assignments Tab -->
                            <div class="tab-pane fade" id="assignments" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Assignments</h5>
                                    <a href="createAssignment.php?course_id=<?= $course_id ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Assignment
                                    </a>
                                </div>
                                
                                <?php if (count($assignments) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Max Points</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($assignments as $assignment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($assignment['title']) ?></td>
                                                <td><?= $assignment['max_points'] ?></td>
                                                <td><?= $assignment['due_date'] ? formatDate($assignment['due_date']) : 'No deadline' ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $assignment['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($assignment['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="assignments.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="deleteAssignment.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-danger"
                                                           onclick="return confirm('Are you sure you want to delete this assignment?');">
                                                            <i class="fas fa-trash"></i> Delete
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
                                    <i class="fas fa-info-circle"></i> No assignments have been created for this course yet.
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Quizzes Tab -->
                            <div class="tab-pane fade" id="quizzes" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Quizzes</h5>
                                    <a href="createQuiz.php?course_id=<?= $course_id ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Quiz
                                    </a>
                                </div>
                                
                                <?php if (count($quizzes) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Passing Score</th>
                                                <th>Time Limit</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($quizzes as $quiz): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($quiz['title']) ?></td>
                                                <td><?= $quiz['passing_score'] ?>%</td>
                                                <td><?= $quiz['time_limit'] ? $quiz['time_limit'] . ' mins' : 'No limit' ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $quiz['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($quiz['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="quizzes.php?id=<?= $quiz['quiz_id'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="deleteQuiz.php?id=<?= $quiz['quiz_id'] ?>" class="btn btn-danger"
                                                           onclick="return confirm('Are you sure you want to delete this quiz?');">
                                                            <i class="fas fa-trash"></i> Delete
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
                                    <i class="fas fa-info-circle"></i> No quizzes have been created for this course yet.
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Exams Tab -->
                            <div class="tab-pane fade" id="exams" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Exams</h5>
                                    <a href="createExam.php?course_id=<?= $course_id ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Exam
                                    </a>
                                </div>
                                
                                <?php if (count($exams) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Total Marks</th>
                                                <th>Exam Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($exams as $exam): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($exam['title']) ?></td>
                                                <td><?= $exam['total_marks'] ?></td>
                                                <td><?= formatDate($exam['exam_date']) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $exam['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                        <?= ucfirst($exam['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="exams.php?id=<?= $exam['exam_id'] ?>" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="deleteExam.php?id=<?= $exam['exam_id'] ?>" class="btn btn-danger"
                                                           onclick="return confirm('Are you sure you want to delete this exam?');">
                                                            <i class="fas fa-trash"></i> Delete
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
                                    <i class="fas fa-info-circle"></i> No exams have been created for this course yet.
                                </div>
                                <?php endif; ?>
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