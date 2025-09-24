<?php
require_once 'includes/functions.php';

$page = (int)($_GET['page'] ?? 1);

// Get all posts for homepage
$result = get_posts($page, 15); // Show 15 posts per page
$posts = $result['posts'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title><?php echo htmlspecialchars(SITE_TITLE . " - Home", ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body class="theme-transition">

    <!-- Enhanced Hero Section -->
    <section class="hero animate-fade-in-up">
        <div class="hero__content">
            <h1 class="hero__title animate-fade-in-up" style="animation-delay: 200ms;">
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="hero__subtitle animate-fade-in-up" style="animation-delay: 400ms;">
                <?php echo htmlspecialchars("Discover amazing stories and insights", ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main-content" class="section">
        <div class="container">
            <?php if (empty($posts)): ?>
                <!-- Empty State -->
                <div class="animate-fade-in-up">
                    <div class="card card--lg mx-auto" style="max-width: 600px;">
                        <div class="card__content text-center">
                            <div class="text-4xl mb-lg">📝</div>
                            <h2 class="text-2xl font-semibold mb-md">
                                No posts yet
                            </h2>
                            <p class="text-muted mb-lg">
                                Be the first to share your thoughts and ideas with the world.
                            </p>
                            <a href="admin/login.php" class="btn btn--primary btn--lg">
                                <span class="icon">🚀</span>
                                Create Your First Post
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Posts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-lg animate-fade-in-up" style="animation-delay: 200ms;">
                    <?php foreach ($posts as $index => $post): ?>
                        <article class="post-card animate-fade-in-up" style="animation-delay: <?php echo 300 + ($index * 100); ?>ms;">
                            <div class="post-card__content">
                                <h2 class="post-card__title">
                                    <a href="<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html">
                                        <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h2>

                                <p class="post-card__excerpt">
                                    <?php echo htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>

                                <div class="post-card__meta">
                                    <time datetime="<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>">
                                        📅 <?php echo htmlspecialchars(date('M j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?>
                                    </time>

                                    <?php if (!empty($post['categories'])): ?>
                                        <span class="post-card__categories">
                                            🏷️ <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-lg">
                                    <a href="<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                       class="btn btn--primary btn--sm">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Enhanced Pagination -->
                <?php if ($result['total_pages'] > 1): ?>
                    <nav class="pagination animate-fade-in-up" style="animation-delay: 800ms;" role="navigation" aria-label="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>"
                               class="pagination__item"
                               aria-label="Previous page">
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
                               aria-label="Page <?php echo $i; ?>"
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
                            <a href="?page=<?php echo $page + 1; ?>"
                               class="pagination__item"
                               aria-label="Next page">
                                Next →
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <div class="mb-lg">
                    <h3 class="text-lg font-semibold mb-sm">Powered by QiBlog</h3>
                    <p class="text-muted">
                        A modern, secure flat-file CMS built with PHP
                    </p>
                </div>

                <!-- Footer links removed as requested -->

                <div class="pt-lg border-t border-border-light">
                    <p class="text-sm text-muted">
                        © <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>.
                        Built with ❤️ using modern web technologies.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        // Simple theme toggle functionality
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
        const body = document.body;

        // Add theme class based on user preference
        if (prefersDark.matches) {
            body.classList.add('theme-dark');
        }

        // Listen for system theme changes
        prefersDark.addEventListener('change', (e) => {
            body.classList.toggle('theme-dark', e.matches);
        });

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading states for buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.href && !this.href.includes('#')) {
                    this.classList.add('btn--loading');
                    setTimeout(() => {
                        this.classList.remove('btn--loading');
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>