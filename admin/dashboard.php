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
$tags = [];

foreach ($posts as $post) {
    $categories = array_merge($categories, $post['categories']);
    $tags = array_merge($tags, $post['tags']);
}

$unique_categories = array_unique($categories);
$unique_tags = array_unique($tags);
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
</head>
<body class="theme-transition admin-dashboard">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Admin Navigation -->
    <nav class="nav">
        <div class="nav__container">
            <div class="nav__brand">
                <span class="nav__logo">⚙️</span>
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?> Admin
            </div>

            <div class="nav__actions">
                <a href="create.php" class="btn btn--primary btn--sm">
                    <span class="icon">✨</span>
                    Create Post
                </a>
                <a href="logout.php" class="btn btn--ghost btn--sm">
                    <span class="icon">🚪</span>
                    Logout
                </a>
                <button class="theme-toggle btn btn--ghost btn--sm" aria-label="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="section section--lg">
        <div class="container">
            <!-- Page Header -->
            <div class="mb-4xl animate-fade-in-up">
                <h1 class="text-3xl font-bold mb-md">Dashboard</h1>
                <p class="text-muted">Manage your blog posts and content</p>
            </div>

            <!-- Message Display -->
            <?php if ($message): ?>
                <div class="notification animate-fade-in-up mb-lg <?php echo strpos($message, 'success') !== false ? 'notification--success' : 'notification--error'; ?>">
                    <div class="flex items-center gap-sm">
                        <span class="icon"><?php echo strpos($message, 'success') !== false ? '✅' : '❌'; ?></span>
                        <span><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-lg mb-4xl animate-fade-in-up" style="animation-delay: 200ms;">
                <div class="stats-card">
                    <div class="stats-card__number"><?php echo $total_posts; ?></div>
                    <div class="stats-card__label">Total Posts</div>
                    <div class="stats-card__icon">📝</div>
                </div>

                <div class="stats-card">
                    <div class="stats-card__number"><?php echo count($unique_categories); ?></div>
                    <div class="stats-card__label">Categories</div>
                    <div class="stats-card__icon">🏷️</div>
                </div>

                <div class="stats-card">
                    <div class="stats-card__number"><?php echo count($unique_tags); ?></div>
                    <div class="stats-card__label">Tags</div>
                    <div class="stats-card__icon">🔖</div>
                </div>

                <div class="stats-card">
                    <div class="stats-card__number"><?php echo count($recent_posts); ?></div>
                    <div class="stats-card__label">Recent Posts</div>
                    <div class="stats-card__icon">🕐</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-4xl animate-fade-in-up" style="animation-delay: 400ms;">
                <h2 class="text-2xl font-semibold mb-lg">Quick Actions</h2>
                <div class="flex flex-wrap gap-md">
                    <a href="create.php" class="btn btn--primary btn--lg">
                        <span class="icon">✨</span>
                        Create New Post
                    </a>
                    <a href="../index.php" class="btn btn--secondary btn--lg" target="_blank">
                        <span class="icon">🌐</span>
                        View Site
                    </a>
                    <a href="../sitemap.xml" class="btn btn--secondary btn--lg" target="_blank">
                        <span class="icon">🗺️</span>
                        Sitemap
                    </a>
                </div>
            </div>

            <!-- Posts Management -->
            <div class="animate-fade-in-up" style="animation-delay: 600ms;">
                <div class="flex justify-between items-center mb-lg">
                    <h2 class="text-2xl font-semibold">Recent Posts</h2>
                    <div class="flex gap-sm">
                        <a href="?page=<?php echo max(1, $page - 1); ?>" class="btn btn--ghost btn--sm <?php echo $page <= 1 ? 'btn--disabled' : ''; ?>">
                            ← Previous
                        </a>
                        <span class="btn btn--ghost btn--sm">
                            Page <?php echo $page; ?> of <?php echo $result['total_pages']; ?>
                        </span>
                        <a href="?page=<?php echo min($result['total_pages'], $page + 1); ?>" class="btn btn--ghost btn--sm <?php echo $page >= $result['total_pages'] ? 'btn--disabled' : ''; ?>">
                            Next →
                        </a>
                    </div>
                </div>

                <?php if (empty($posts)): ?>
                    <!-- Empty State -->
                    <div class="card card--lg mx-auto" style="max-width: 600px;">
                        <div class="card__content text-center">
                            <div class="text-4xl mb-lg">📝</div>
                            <h3 class="text-xl font-semibold mb-md">No posts yet</h3>
                            <p class="text-muted mb-lg">
                                Start creating content to see your posts here.
                            </p>
                            <a href="create.php" class="btn btn--primary btn--lg">
                                <span class="icon">🚀</span>
                                Create Your First Post
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Posts Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg">
                        <?php foreach ($posts as $index => $post): ?>
                            <article class="admin-card animate-fade-in-up" style="animation-delay: <?php echo 700 + ($index * 100); ?>ms;">
                                <div class="admin-card__header">
                                    <h3 class="font-semibold truncate" title="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <div class="text-sm text-muted">
                                        📅 <?php echo htmlspecialchars(date('M j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>

                                <div class="admin-card__body">
                                    <?php if (!empty($post['categories'])): ?>
                                        <div class="mb-md">
                                            <span class="text-sm font-medium text-muted">Categories:</span>
                                            <div class="flex flex-wrap gap-xs mt-xs">
                                                <?php foreach ($post['categories'] as $category): ?>
                                                    <span class="px-sm py-xs bg-secondary text-xs rounded">
                                                        <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <p class="text-sm text-muted line-clamp-2">
                                        <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150), ENT_QUOTES, 'UTF-8'); ?>...
                                    </p>
                                </div>

                                <div class="admin-card__footer">
                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-xs">
                                            <a href="edit.php?slug=<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                                               class="btn btn--primary btn--xs">
                                                <span class="icon">✏️</span>
                                                Edit
                                            </a>
                                            <a href="../post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                               class="btn btn--secondary btn--xs"
                                               target="_blank">
                                                <span class="icon">👁️</span>
                                                View
                                            </a>
                                        </div>

                                        <button class="btn btn--danger btn--xs"
                                                onclick="deletePost('<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo $page; ?>)">
                                            <span class="icon">🗑️</span>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($result['total_pages'] > 1): ?>
                        <div class="flex justify-center mt-4xl animate-fade-in-up" style="animation-delay: 1000ms;">
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