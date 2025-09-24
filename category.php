<?php
require_once 'includes/functions.php';

$page = (int)($_GET['page'] ?? 1);
$category = $_GET['category'] ?? null;

// Validate category parameter
if (!$category) {
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Category Not Found - <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/style.css">
    </head>
    <body class="theme-transition">
        <div class="hero">
            <div class="hero__content">
                <h1 class="hero__title">404 - Category Not Found</h1>
                <p class="hero__subtitle">The category you're looking for doesn't exist.</p>
                <a href="/" class="btn btn--accent btn--lg">
                    <span class="icon">🏠</span>
                    Back to Home
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get posts for this category
$result = get_posts_by_category($category, $page, 15);
$posts = $result['posts'];
$total_posts = $result['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags(null, $category); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Enhanced Structured Data for Category -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "<?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?>",
        "description": "Browse all posts in the <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?> category",
        "url": "<?php echo SITE_URL; ?>/<?php echo urlencode($category); ?>/",
        "mainEntity": {
            "@type": "ItemList",
            "numberOfItems": <?php echo $total_posts; ?>,
            "itemListElement": [
                <?php foreach ($posts as $index => $post): ?>
                {
                    "@type": "ListItem",
                    "position": <?php echo $index + 1; ?>,
                    "item": {
                        "@type": "Article",
                        "name": "<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>",
                        "url": "<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html",
                        "datePublished": "<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>",
                        "description": "<?php echo htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8'); ?>"
                    }
                }<?php echo $index < count($posts) - 1 ? ',' : ''; ?>

                <?php endforeach; ?>
            ]
        }
    }
    </script>

    <title><?php echo htmlspecialchars(ucfirst($category) . ' | ' . SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body class="theme-transition">

    <!-- Reading Progress Bar -->
    <div class="reading-progress">
        <div class="reading-progress__bar"></div>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav__container">
            <a href="/index.php" class="nav__brand">
                <span class="nav__logo">📖</span>
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>
            </a>

            <div class="nav__actions">
                <a href="/index.php" class="btn btn--ghost btn--sm">
                    <span class="icon">🏠</span>
                    Home
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
            <!-- Category Header -->
            <div class="mb-4xl text-center animate-fade-in-up">
                <h1 class="text-4xl font-bold mb-lg">
                    <?php echo htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="text-xl text-muted mb-xl">
                    <?php echo $total_posts; ?> post<?php echo $total_posts !== 1 ? 's' : ''; ?> in this category
                </p>
                <a href="/index.php" class="btn btn--secondary btn--lg">
                    <span class="icon">←</span>
                    View All Posts
                </a>
            </div>

            <?php if (empty($posts)): ?>
                <!-- Empty State -->
                <div class="animate-fade-in-up">
                    <div class="card card--lg mx-auto" style="max-width: 600px;">
                        <div class="card__content text-center">
                            <div class="text-4xl mb-lg">📝</div>
                            <h2 class="text-2xl font-semibold mb-md">
                                No posts in <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?> category
                            </h2>
                            <p class="text-muted mb-lg">
                                There are no posts in this category yet.
                            </p>
                            <a href="/index.php" class="btn btn--primary btn--lg">
                                <span class="icon">←</span>
                                View All Posts
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
                                    <a href="/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html">
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
                                    <a href="/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
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
                            <a href="/<?php echo urlencode($category); ?>/?page=<?php echo $page - 1; ?>"
                               class="pagination__item"
                               aria-label="Previous page">
                                ← Previous
                            </a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($result['total_pages'], $page + 2);

                        if ($start_page > 1): ?>
                            <a href="/<?php echo urlencode($category); ?>/" class="pagination__item">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination__ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="/<?php echo urlencode($category); ?>/?page=<?php echo $i; ?>"
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
                            <a href="/<?php echo urlencode($category); ?>/?page=<?php echo $result['total_pages']; ?>" class="pagination__item">
                                <?php echo $result['total_pages']; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($page < $result['total_pages']): ?>
                            <a href="/<?php echo urlencode($category); ?>/?page=<?php echo $page + 1; ?>"
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

    <!-- Enhanced Scripts -->
    <script>
        // Reading Progress Bar
        function updateReadingProgress() {
            const progressBar = document.querySelector('.reading-progress__bar');
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            progressBar.style.width = scrollPercent + '%';
        }

        // Theme Toggle
        function initThemeToggle() {
            const themeToggle = document.querySelector('.theme-toggle');
            const themeIcon = document.querySelector('.theme-icon');
            const body = document.body;

            // Check for saved theme preference or default to system
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                body.className = savedTheme === 'dark' ? 'theme-dark' : '';
                updateThemeIcon(themeIcon, savedTheme === 'dark');
            } else if (systemPrefersDark) {
                body.className = 'theme-dark';
                updateThemeIcon(themeIcon, true);
            }

            themeToggle.addEventListener('click', () => {
                const isDark = body.classList.toggle('theme-dark');
                updateThemeIcon(themeIcon, isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }

        function updateThemeIcon(icon, isDark) {
            icon.textContent = isDark ? '☀️' : '🌙';
        }

        // Smooth scrolling for anchor links
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

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            updateReadingProgress();
            initThemeToggle();

            window.addEventListener('scroll', updateReadingProgress);
            window.addEventListener('resize', updateReadingProgress);
        });
    </script>

    <!-- Reading Progress Bar Styles -->
    <style>
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--color-bg-secondary);
            z-index: var(--z-fixed);
        }

        .reading-progress__bar {
            height: 100%;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
            width: 0%;
            transition: width 0.1s ease;
        }

        .theme-dark .reading-progress__bar {
            background: linear-gradient(90deg, var(--color-accent), var(--color-accent-light));
        }
    </style>
</body>
</html>