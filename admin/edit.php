<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$message = '';
$post = null;

if (isset($_GET['slug'])) {
    $post = get_post_by_slug($_GET['slug']);
}

if (!$post) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $data = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'categories' => explode(',', $_POST['categories']),
            'tags' => explode(',', $_POST['tags'])
        ];

        // Delete old file if slug changed
        if (create_slug($_POST['title']) !== $post['slug']) {
            unlink($post['file']);
        }

        if (save_post($data)) {
            header('Location: dashboard.php?message=Post updated successfully');
            exit;
        } else {
            $message = 'Failed to save post. Please ensure title is not empty.';
        }
    } else {
        $message = 'CSRF token invalid';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?> Admin</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Edit Post</h1>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="categories" class="form-label">Categories (comma-separated)</label>
                <input type="text" class="form-control" id="categories" name="categories" value="<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Post</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
        <script>
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'sourceEditing', 'undo', 'redo'],
                    simpleUpload: {
                        uploadUrl: 'upload.php'
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        </script>
    </div>
</body>
</html>