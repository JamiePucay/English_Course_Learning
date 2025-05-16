<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!Auth::isLoggedIn() || !Auth::hasRole('teacher')) {
    setFlashMessage('error', 'Unauthorized access');
    redirect('../user/login.php');
}

$user = Auth::getCurrentUser();
$teacher_id = $user['user_id'];

if (!isset($_GET['course_id'])) {
    setFlashMessage('error', 'Missing course ID');
    redirect('courses.php');
}

$course_id = (int) $_GET['course_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken($_POST['csrf']);
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $sequence_order = (int) $_POST['sequence_order'];
    $duration = (int) $_POST['duration'];
    $status = in_array($_POST['status'], ['active', 'locked']) ? $_POST['status'] : 'active';

    // Insert the lesson
    $lesson_id = executeQuerySingle("INSERT INTO lessons (course_id, title, content, sequence_order, duration, status) 
        VALUES (?, ?, ?, ?, ?, ?)", 
        [$course_id, $title, $content, $sequence_order, $duration, $status]);

    // Handle file uploads
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['name'] as $i => $name) {
            $file = [
                'name'     => $_FILES['attachments']['name'][$i],
                'type'     => $_FILES['attachments']['type'][$i],
                'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                'error'    => $_FILES['attachments']['error'][$i],
                'size'     => $_FILES['attachments']['size'][$i]
            ];

            $upload = uploadFile($file, UPLOAD_DIR);
            if ($upload) {
                $content_type = getContentTypeByExtension($upload['extension']);
                executeNonQuery(
                    "INSERT INTO lesson_content (lesson_id, content_type, content_title, file_path, sequence_order, uploaded_by) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$lesson_id, $content_type, $upload['original_name'], $upload['new_name'], 0, $teacher_id]
                );
            }
        }
    }
    setFlashMessage('success', 'Lesson created');
    redirect("editCourse.php?id=$course_id");
}

function getContentTypeByExtension($ext) {
    $map = [
        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image',
        'pdf' => 'document', 'doc' => 'document', 'docx' => 'document',
        'ppt' => 'document', 'pptx' => 'document',
        'mp3' => 'audio', 'mp4' => 'video'
    ];
    return $map[$ext] ?? 'other';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create a Lesson</title>
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
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Create a Lesson</h5>
                        </div>
                        <div class="card-body">
                            <!-- HTML Form -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
                                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Sequence Order</label>
                                    <input type="number" name="sequence_order" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Duration (minutes)</label>
                                    <input type="number" name="duration" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="locked">Locked</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lesson Description</label>
                                    <textarea name="content" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Upload Files</label>
                                    <input type="file" name="attachments[]" multiple class="form-control">
                                    <small>Allowed: <?= implode(', ', ALLOWED_EXTENSIONS) ?> | Max size: <?= MAX_FILE_SIZE / 50000000 ?>MB</small>
                                </div>
                                <button type="submit" class="btn btn-success">Create Lesson</button>
                            </form>
                        </div>
                    </div>
                </div>    
            </main>        
        </div>
    </div>       
</div>

</body>
</html>