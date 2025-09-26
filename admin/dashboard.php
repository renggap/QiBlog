<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$page = (int)($_GET['page'] ?? 1);
$result = get_posts($page, 20); // 20 posts per page for admin
$posts = $result['posts'];
$message = '';

if (isset($_GET['delete'])) {
    if (delete_post($_GET['delete'])) {
        $message = 'Post deleted successfully';
        $result = get_posts($page, 20); // Refresh list
        $posts = $result['posts'];
        if (empty($posts) && $page > 1) {
            header('Location: ?page=' . ($page - 1));
            exit;
        }
    } else {
        $message = 'Failed to delete post';
    }
}

// Get statistics
$total_posts = count(get_posts(1, 1000)['posts']);
$recent_posts = array_slice($posts, 0, 5);
$categories = [];

foreach ($posts as $post) {
    $categories = array_merge($categories, $post['categories']);
}

$unique_categories = array_unique($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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

        .admin-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-dashboard.theme-dark {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        .container {
            max-width: 1200px;
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

        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card__number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 0.5rem;
        }

        .stats-card__label {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .stats-card__icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        /* Quick Actions */
        .quick-actions {
            margin-bottom: 3rem;
        }

        .quick-actions h2 {
            color: white;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .actions-grid {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn--lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Posts Section */
        .posts-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .posts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .posts-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        .pagination-controls {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination-controls .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .pagination-controls .btn--disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Admin Cards */
        .admin-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .admin-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .admin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .admin-card__header {
            margin-bottom: 1rem;
        }

        .admin-card__header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .admin-card__header .date {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .admin-card__body {
            margin-bottom: 1.5rem;
        }

        .admin-card__body .categories {
            margin-bottom: 1rem;
        }

        .admin-card__body .categories-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .admin-card__body .category-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .category-tag {
            background: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .admin-card__body .content-preview {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .admin-card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-card__footer .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn--xs {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn--secondary {
            background: #f9fafb;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn--secondary:hover {
            background: #f3f4f6;
        }

        .btn--danger {
            background: #ef4444;
            color: white;
        }

        .btn--danger:hover {
            background: #dc2626;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination__item {
            padding: 0.5rem 1rem;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #4f46e5;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            font-weight: 500;
        }

        .pagination__item:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .pagination__item--active {
            background: white;
            color: #4f46e5;
        }

        .pagination__ellipsis {
            color: #6b7280;
            padding: 0 0.5rem;
        }

        /* Notification */
        .notification {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #10b981;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .notification--error {
            border-left-color: #ef4444;
            color: #991b1b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .admin-cards-grid {
                grid-template-columns: 1fr;
            }

            .nav__container {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .posts-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-card__footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body class="admin-dashboard">
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
                <a href="create.php" class="btn btn--primary">
                    <span>✨</span>
                    Create Post
                </a>
                <a href="logout.php" class="btn btn--ghost">
                    <span>🚪</span>
                    Logout
                </a>
                <button class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Manage your blog posts and content</p>
            </div>

            <!-- Message Display -->
            <?php if ($message): ?>
                <div class="notification">
                    <span><?php echo strpos($message, 'success') !== false ? '✅' : '❌'; ?></span>
                    <span><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stats-card">
                    <div class="stats-card__number"><?php echo $total_posts; ?></div>
                    <div class="stats-card__label">Total Posts</div>
                    <div class="stats-card__icon">📝</div>
                </div>

                <div class="stats-card">
                    <div class="stats-card__number"><?php echo count($unique_categories); ?></div>
                    <div class="stats-card__label">Categories</div>
                    <div class="stats-card__icon">📂</div>
                </div>

                <div class="stats-card">
                    <div class="stats-card__number"><?php echo count($recent_posts); ?></div>
                    <div class="stats-card__label">Recent Posts</div>
                    <div class="stats-card__icon">🕐</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="create.php" class="btn btn--primary btn--lg">
                        <span>✨</span>
                        Create New Post
                    </a>
                    <a href="../index.php" class="btn btn--secondary btn--lg" target="_blank">
                        <span>🌐</span>
                        View Site
                    </a>
                </div>
            </div>

            <!-- Posts Management -->
            <div class="posts-section">
                <div class="posts-header">
                    <h2>Recent Posts</h2>
                    <div class="pagination-controls">
                        <a href="?page=<?php echo max(1, $page - 1); ?>" class="btn btn--ghost <?php echo $page <= 1 ? 'btn--disabled' : ''; ?>">
                            ← Previous
                        </a>
                        <span class="btn btn--ghost">
                            Page <?php echo $page; ?> of <?php echo $result['total_pages']; ?>
                        </span>
                        <a href="?page=<?php echo min($result['total_pages'], $page + 1); ?>" class="btn btn--ghost <?php echo $page >= $result['total_pages'] ? 'btn--disabled' : ''; ?>">
                            Next →
                        </a>
                    </div>
                </div>

                <?php if (empty($posts)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="icon">📝</div>
                        <h3>No posts yet</h3>
                        <p>
                            Start creating content to see your posts here.
                        </p>
                        <a href="create.php" class="btn btn--primary btn--lg">
                            <span>🚀</span>
                            Create Your First Post
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Posts Grid -->
                    <div class="admin-cards-grid">
                        <?php foreach ($posts as $index => $post): ?>
                            <article class="admin-card">
                                <div class="admin-card__header">
                                    <h3><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <div class="date">📅 <?php echo htmlspecialchars(date('M j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <div class="admin-card__body">
                                    <?php if (!empty($post['categories'])): ?>
                                        <div class="categories">
                                            <div class="categories-label">Categories:</div>
                                            <div class="category-tags">
                                                <?php foreach ($post['categories'] as $category): ?>
                                                    <span class="category-tag"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <p class="content-preview">
                                        <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150), ENT_QUOTES, 'UTF-8'); ?>...
                                    </p>
                                </div>

                                <div class="admin-card__footer">
                                    <div class="actions">
                                        <a href="edit.php?slug=<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                                           class="btn btn--primary btn--xs">
                                            <span>✏️</span>
                                            Edit
                                        </a>
                                        <a href="../post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                           class="btn btn--secondary btn--xs"
                                           target="_blank">
                                            <span>👁️</span>
                                            View
                                        </a>
                                    </div>

                                    <button class="btn btn--danger btn--xs"
                                            onclick="deletePost('<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo $page; ?>)">
                                        <span>🗑️</span>
                                        Delete
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($result['total_pages'] > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="pagination__item">
                                    ← Previous
                                </a>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($result['total_pages'], $page + 2);

                            if ($start_page > 1): ?>
                                <a href="?page=1" class="pagination__item">1</a>
                                <?php if ($start_page > 2): ?>
                                    <span class="pagination__ellipsis">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="?page=<?php echo $i; ?>"
                                   class="pagination__item <?php echo $i == $page ? 'pagination__item--active' : ''; ?>"
                                   aria-current="<?php echo $i == $page ? 'page' : 'false'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($end_page < $result['total_pages']): ?>
                                <?php if ($end_page < $result['total_pages'] - 1): ?>
                                    <span class="pagination__ellipsis">...</span>
                                <?php endif; ?>
                                <a href="?page=<?php echo $result['total_pages']; ?>" class="pagination__item">
                                    <?php echo $result['total_pages']; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($page < $result['total_pages']): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="pagination__item">
                                    Next →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Enhanced Scripts -->
    <script>
        // Theme Toggle
        function initThemeToggle() {
            const themeToggle = document.querySelector('.theme-toggle');
            const themeIcon = document.querySelector('.theme-icon');
            const body = document.body;

            const savedTheme = localStorage.getItem('admin-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                body.className = savedTheme === 'dark' ? 'theme-dark admin-dashboard' : 'admin-dashboard';
                updateThemeIcon(themeIcon, savedTheme === 'dark');
            } else if (systemPrefersDark) {
                body.className = 'theme-dark admin-dashboard';
                updateThemeIcon(themeIcon, true);
            }

            themeToggle.addEventListener('click', () => {
                const isDark = body.classList.toggle('theme-dark');
                updateThemeIcon(themeIcon, isDark);
                localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
            });
        }

        function updateThemeIcon(icon, isDark) {
            icon.textContent = isDark ? '☀️' : '🌙';
        }

        // Delete Post Function
        function deletePost(slug, currentPage) {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                // Create a loading state
                const deleteButtons = document.querySelectorAll(`[onclick*="deletePost"]`);
                deleteButtons.forEach(btn => btn.disabled = true);

                // Redirect to delete URL
                window.location.href = `?page=${currentPage}&delete=${encodeURIComponent(slug)}`;
            }
        }

        // Add smooth animations
        function addHoverEffects() {
            const cards = document.querySelectorAll('.admin-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-2px)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            initThemeToggle();
            addHoverEffects();

            // Add loading states for buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (this.href && !this.href.includes('#')) {
                        this.classList.add('btn--loading');
                    }
                });
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                // Clear any loading states
                document.querySelectorAll('.btn--loading').forEach(btn => {
                    btn.classList.remove('btn--loading');
                });
            }
        });
    </script>

    <!-- Notification Styles -->
    <style>
        .notification {
            padding: var(--space-lg);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
        }

        .notification--success {
            background-color: rgba(107, 142, 35, 0.1);
            border: 1px solid var(--color-success);
            color: var(--color-success);
        }

        .notification--error {
            background-color: rgba(160, 82, 45, 0.1);
            border: 1px solid var(--color-danger);
            color: var(--color-danger);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .admin-dashboard {
            background-color: var(--color-bg-secondary);
            min-height: 100vh;
        }

        .theme-dark.admin-dashboard {
            background-color: var(--color-bg-primary);
        }
    </style>
</body>
</html>