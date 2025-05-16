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

$lesson_id = (int) $_GET['id'];
$lesson = executeQuerySingle("SELECT * FROM lessons WHERE lesson_id = ?", [$lesson_id]);
$attachments = executeQueryAll("SELECT * FROM lesson_content WHERE lesson_id = ? ORDER BY sequence_order ASC", [$lesson_id]);

if (!$lesson) {
    setFlashMessage('error', 'Lesson not found');
    redirect('courses.php');
}

$course_id = $lesson['course_id'];

// Get the current highest sequence order value for the lesson's content
$highestSequence = 0;
if (!empty($attachments)) {
    foreach ($attachments as $attachment) {
        if ($attachment['sequence_order'] > $highestSequence) {
            $highestSequence = $attachment['sequence_order'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRFToken($_POST['csrf']);
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $sequence_order = (int) $_POST['sequence_order'];
    $duration = (int) $_POST['duration'];
    $status = in_array($_POST['status'], ['active', 'locked']) ? $_POST['status'] : 'active';

    executeNonQuery("UPDATE lessons SET title = ?, content = ?, sequence_order = ?, duration = ?, status = ? WHERE lesson_id = ?", 
        [$title, $content, $sequence_order, $duration, $status, $lesson_id]);

    $uploadErrors = [];
    $successfulUploads = 0;
    
    // Handle file uploads
    if (!empty($_FILES['attachments']['name'][0])) {
        // Create upload directory if it doesn't exist
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        // Get the next sequence order for new attachments
        $nextSequenceOrder = $highestSequence + 1;
        
        foreach ($_FILES['attachments']['name'] as $i => $name) {
            if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) {
                $errorMessage = getFileUploadErrorMessage($_FILES['attachments']['error'][$i]);
                $uploadErrors[] = "Error uploading {$name}: {$errorMessage}";
                continue;
            }
            
            $file = [
                'name'     => $_FILES['attachments']['name'][$i],
                'type'     => $_FILES['attachments']['type'][$i],
                'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                'error'    => $_FILES['attachments']['error'][$i],
                'size'     => $_FILES['attachments']['size'][$i]
            ];

            // Validate file extension
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
                $uploadErrors[] = "File type '{$fileExt}' not allowed for {$file['name']}";
                continue;
            }
            
            // Validate file size
            if ($file['size'] > MAX_FILE_SIZE) {
                $maxSizeMB = MAX_FILE_SIZE / 1048576; // Convert to MB
                $uploadErrors[] = "File {$file['name']} exceeds maximum size of {$maxSizeMB} MB";
                continue;
            }

            $upload = uploadFile($file, UPLOAD_DIR);
            if ($upload) {
                $content_type = getContentTypeByExtension($upload['extension']);
                $result = executeNonQuery(
                    "INSERT INTO lesson_content (lesson_id, content_type, content_title, file_path, sequence_order, uploaded_by) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$lesson_id, $content_type, $upload['original_name'], $upload['path'], $nextSequenceOrder, $teacher_id]
                );
                
                if ($result) {
                    $successfulUploads++;
                    $nextSequenceOrder++;
                } else {
                    $uploadErrors[] = "Database error: Failed to save attachment {$upload['original_name']}";
                }
            } else {
                $uploadErrors[] = "Failed to upload {$file['name']}";
            }
        }
    }
    
    // Set appropriate flash messages
    if (!empty($uploadErrors)) {
        // Convert array of errors to string
        $errorString = implode(', ', $uploadErrors);
        setFlashMessage('error', "Some files could not be uploaded: {$errorString}");
    }
    
    if ($successfulUploads > 0) {
        setFlashMessage('success', "Lesson updated successfully with {$successfulUploads} new attachment(s)");
    } else {
        setFlashMessage('success', 'Lesson updated successfully');
    }
    
    redirect("editCourse.php?id=$course_id");
}

/**
 * Get error message for file upload error code
 *
 * @param int $error Error code from $_FILES['file']['error']
 * @return string Error message
 */
function getFileUploadErrorMessage($error) {
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "A PHP extension stopped the file upload";
        default:
            return "Unknown upload error";
    }
}

/**
 * Map file extension to content type
 * 
 * @param string $ext File extension
 * @return string Content type
 */
function getContentTypeByExtension($ext) {
    $map = [
        // Images
        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image', 'svg' => 'image',
        // Documents
        'pdf' => 'document', 'doc' => 'document', 'docx' => 'document', 'txt' => 'document',
        'ppt' => 'document', 'pptx' => 'document', 'xlsx' => 'document', 'xls' => 'document',
        // Audio
        'mp3' => 'audio', 'wav' => 'audio', 'ogg' => 'audio',
        // Video
        'mp4' => 'video', 'avi' => 'video', 'mov' => 'video', 'wmv' => 'video'
    ];
    return $map[strtolower($ext)] ?? 'other';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Lesson</title>
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
                    <div class="card mb-4 w-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit Lesson</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Display flash messages
                            if (isset($_SESSION['flash_message'])): 
                                $messageType = isset($_SESSION['flash_message_type']) ? $_SESSION['flash_message_type'] : 'info';
                                $message = $_SESSION['flash_message'];
                                // Check if message is an array and convert to string if needed
                                if (is_array($message)) {
                                    $message = implode('<br>', $message);
                                }
                            ?>
                                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                                    <?= $message ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                                <?php unset($_SESSION['flash_message'], $_SESSION['flash_message_type']); ?>
                            <?php endif; ?>
                            
                            
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Sequence Order</label>
                                    <input type="number" name="sequence_order" value="<?= $lesson['sequence_order'] ?>" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Duration (minutes)</label>
                                    <input type="number" name="duration" value="<?= $lesson['duration'] ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="active" <?= $lesson['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="locked" <?= $lesson['status'] === 'locked' ? 'selected' : '' ?>>Locked</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lesson Description</label>
                                    <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($lesson['content']) ?></textarea>
                                </div>
                                
                                <?php if (!empty($attachments)): ?>
                                    <div class="form-group">
                                        <label>Existing Attachments</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Sequence</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($attachments as $att): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($att['content_title']) ?></td>
                                                            <td><span class="badge badge-info"><?= $att['content_type'] ?></span></td>
                                                            <td><?= $att['sequence_order'] ?></td>
                                                            <td>
                                                                <form method="POST" action="deleteAttachment.php" class="d-inline">
                                                                    <input type="hidden" name="csrf" value="<?= createCSRFToken() ?>">
                                                                    <input type="hidden" name="content_id" value="<?= $att['content_id'] ?>">
                                                                    <input type="hidden" name="lesson_id" value="<?= $lesson_id ?>">
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this attachment?');">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label>Add New Files</label>
                                    <div class="custom-file">
                                        <input type="file" name="attachments[]" multiple class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Choose files</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Allowed file types: 
                                        <?= implode(', ', ALLOWED_EXTENSIONS) ?>. 
                                        Maximum size: <?= MAX_FILE_SIZE / 1048576 ?> MB.
                                    </small>
                                </div>
                                
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Lesson
                                    </button>
                                    <a href="editCourse.php?id=<?= $course_id ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Course
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>    
            </main>        
        </div>
    </div>       

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
// Display file name when file is selected
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var files = [];
    for (var i = 0; i < this.files.length; i++) {
        files.push(this.files[i].name);
    }
    var fileLabel = document.querySelector('.custom-file-label');
    if (files.length > 1) {
        fileLabel.textContent = files.length + ' files selected';
    } else if (files.length === 1) {
        fileLabel.textContent = files[0];
    } else {
        fileLabel.textContent = 'Choose files';
    }
});
</script>
</body>
</html>