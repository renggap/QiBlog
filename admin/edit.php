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
            'categories' => array_filter(array_map('trim', explode(',', $_POST['categories'] ?? ''))),
            'tags' => [] // Remove tags functionality
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
</head>
<body>
    <nav class="nav">
        <div class="nav__container">
            <a href="dashboard.php" class="nav__brand">
                <span class="nav__logo">⚙️</span>
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?> Admin
            </a>
            <div class="nav__actions">
                <a href="dashboard.php" class="btn btn--ghost btn--sm">
                    <span class="icon">←</span>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <main class="section section--lg">
        <div class="container">
            <div class="animate-fade-in-up">
                <h1 class="text-3xl font-bold mb-lg">Edit Post</h1>

                <?php if ($message): ?>
                    <div class="notification notification--error mb-lg">
                        <div class="flex items-center gap-sm">
                            <span class="icon">❌</span>
                            <span><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" class="card">
                    <div class="card__content">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                        <div class="form-group">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-input" id="title" name="title"
                                   value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required
                                   placeholder="Enter your post title...">
                        </div>

                        <div class="form-group">
                            <label for="categories" class="form-label">Categories</label>
                            <input type="text" class="form-input" id="categories" name="categories"
                                   value="<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>"
                                   placeholder="e.g., Technology, Web Development, PHP (comma-separated)">
                            <small class="text-muted">Separate multiple categories with commas</small>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label">Content *</label>
                            <textarea class="form-textarea" id="content" name="content"
                                      placeholder="Write your post content here..." rows="20"><?php echo htmlspecialchars($post['content']); ?></textarea>
                        </div>

                        <div class="flex gap-md">
                            <button type="submit" class="btn btn--primary btn--lg">
                                <span class="icon">✏️</span>
                                Update Post
                            </button>
                            <a href="dashboard.php" class="btn btn--ghost btn--lg">
                                <span class="icon">←</span>
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
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

            // Theme toggle functionality
            const themeToggle = document.querySelector('.theme-toggle');
            const body = document.body;

            // Check for saved theme preference
            const savedTheme = localStorage.getItem('admin-theme');
            if (savedTheme) {
                body.className = savedTheme === 'dark' ? 'theme-dark' : '';
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const isDark = body.classList.toggle('theme-dark');
                    localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
                });
            }
        </script>

        <style>
            .notification {
                padding: var(--space-lg);
                border-radius: var(--radius-md);
                margin-bottom: var(--space-lg);
            }

            .notification--error {
                background-color: rgba(160, 82, 45, 0.1);
                border: 1px solid var(--color-danger);
                color: var(--color-danger);
            }

            .form-group {
                margin-bottom: var(--space-xl);
            }

            .form-label {
                display: block;
                margin-bottom: var(--space-sm);
                font-weight: var(--font-weight-medium);
                color: var(--color-text-primary);
            }

            .form-input, .form-textarea {
                width: 100%;
                padding: var(--space-md);
                border: 1px solid var(--color-border-medium);
                border-radius: var(--radius-md);
                font-size: var(--text-base);
                background-color: var(--color-bg-primary);
                color: var(--color-text-primary);
                transition: border-color var(--transition-fast);
            }

            .form-input:focus, .form-textarea:focus {
                outline: none;
                border-color: var(--color-primary);
                box-shadow: 0 0 0 3px rgba(139, 105, 20, 0.1);
            }

            .form-textarea {
                min-height: 300px;
                resize: vertical;
                font-family: var(--font-family-primary);
            }

            .text-muted {
                color: var(--color-text-muted);
                font-size: var(--text-sm);
            }
        </style>
    </div>
</body>
</html>