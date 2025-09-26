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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .admin-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-edit.theme-dark {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Modern Navigation */
        .nav {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .nav__container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav__brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .nav__logo {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .nav__actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn--primary {
            background: white;
            color: #4f46e5;
        }

        .btn--primary:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        .btn--ghost {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn--ghost:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Floating Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-circle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-circle:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-circle:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 15%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9fafb;
            color: #374151;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #4f46e5;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-textarea {
            min-height: 300px;
            resize: vertical;
            line-height: 1.6;
        }

        .form-hint {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn--lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Notification */
        .notification {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn--lg {
                width: 100%;
                justify-content: center;
            }

            .nav__container {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="admin-edit">
    <div class="floating-elements">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <!-- Admin Navigation -->
    <nav class="nav">
        <div class="nav__container">
            <a href="dashboard.php" class="nav__brand">
                <div class="nav__logo">⚙️</div>
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?> Admin
            </a>
            <div class="nav__actions">
                <a href="dashboard.php" class="btn btn--ghost">
                    <span>←</span>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Edit Post</h1>
                <p>Update your content and make changes</p>
            </div>

            <?php if ($message): ?>
                <div class="notification">
                    <span>❌</span>
                    <span><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <!-- Edit Post Form -->
            <div class="form-container">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <div class="form-group">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-input" id="title" name="title"
                               value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required
                               placeholder="Enter your post title..." autofocus>
                    </div>

                    <div class="form-group">
                        <label for="categories" class="form-label">Categories</label>
                        <input type="text" class="form-input" id="categories" name="categories"
                               value="<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="e.g., Technology, Web Development, PHP (comma-separated)">
                        <span class="form-hint">Separate multiple categories with commas</span>
                    </div>

                    <div class="form-group">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-textarea" id="content" name="content"
                                  placeholder="Write your post content here..." required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="dashboard.php" class="btn btn--ghost btn--lg">
                            <span>←</span>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn--primary btn--lg">
                            <span>✏️</span>
                            Update Post
                        </button>
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