<?php
require_once 'includes/functions.php';

$post = null;
if (isset($_GET['slug'])) {
    $post = get_post_by_slug($_GET['slug']);
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Post Not Found - <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="theme-transition">
        <div class="hero">
            <div class="hero__content">
                <h1 class="hero__title">404 - Post Not Found</h1>
                <p class="hero__subtitle">The post you're looking for doesn't exist.</p>
                <a href="index.php" class="btn btn--accent btn--lg">
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags($post); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Fira+Code:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Enhanced Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>",
        "datePublished": "<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>",
        "dateModified": "<?php echo htmlspecialchars($post['modified'], ENT_QUOTES, 'UTF-8'); ?>",
        "author": {
            "@type": "Person",
            "name": "Admin"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo SITE_URL; ?>/assets/images/logo.png"
            }
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?php echo SITE_URL; ?>/post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
        },
        "articleSection": "<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>",
        "keywords": "<?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?>"
    }
    </script>

    <title><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body class="theme-transition">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Reading Progress Bar -->
    <div class="reading-progress">
        <div class="reading-progress__bar"></div>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav__container">
            <a href="index.php" class="nav__brand">
                <span class="nav__logo">📖</span>
                <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>
            </a>

            <div class="nav__actions">
                <a href="index.php" class="btn btn--ghost btn--sm">
                    <span class="icon">🏠</span>
                    Home
                </a>
                <button class="theme-toggle btn btn--ghost btn--sm" aria-label="Toggle dark mode">
                    <span class="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="breadcrumb">
        <div class="breadcrumbs__list">
            <li class="breadcrumbs__item">
                <a href="index.php" class="breadcrumbs__link">Home</a>
            </li>
            <li class="breadcrumbs__separator" aria-hidden="true">→</li>
            <li class="breadcrumbs__item">
                <span class="breadcrumbs__current" aria-current="page">
                    <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </li>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="section section--lg">
        <article class="container">
            <div class="mx-auto" style="max-width: 800px;">
                <!-- Article Header -->
                <header class="mb-4xl animate-fade-in-up">
                    <h1 class="text-4xl font-bold mb-lg leading-tight">
                        <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </h1>

                    <div class="flex flex-wrap items-center gap-md text-muted mb-xl">
                        <time datetime="<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>">
                            📅 <?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?>
                        </time>

                        <?php if (!empty($post['categories'])): ?>
                            <span class="flex items-center gap-sm">
                                🏷️
                                <?php foreach ($post['categories'] as $category): ?>
                                    <span class="px-sm py-xs bg-secondary text-sm rounded">
                                        <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($post['tags'])): ?>
                        <div class="flex flex-wrap gap-sm">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <a href="?tag=<?php echo urlencode($tag); ?>"
                                   class="btn btn--ghost btn--xs">
                                    #<?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </header>

                <!-- Article Content -->
                <div class="post-content animate-fade-in-up" style="animation-delay: 200ms;">
                    <?php
                    // Define allowed HTML tags for content from CKEditor
                    $allowed_html_tags = '<h1><h2><h3><h4><h5><h6><p><a><br><strong><em><ul><ol><li><blockquote><img><pre><code><table><thead><tbody><tr><th><td><hr><figure><figcaption>';
                    echo strip_tags($post['content'], $allowed_html_tags);
                    ?>
                </div>

                <!-- Article Footer -->
                <footer class="mt-4xl pt-4xl border-t border-border-light animate-fade-in-up" style="animation-delay: 400ms;">
                    <!-- Social Sharing -->
                    <div class="mb-4xl">
                        <h3 class="text-xl font-semibold mb-lg">Share this article</h3>
                        <div class="flex flex-wrap gap-md">
                            <button class="share-btn btn btn--secondary btn--sm"
                                    data-url="<?php echo SITE_URL; ?>/post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                    data-title="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                    onclick="shareArticle('twitter', this.dataset.url, this.dataset.title)">
                                <span class="icon">🐦</span>
                                Twitter
                            </button>
                            <button class="share-btn btn btn--secondary btn--sm"
                                    data-url="<?php echo SITE_URL; ?>/post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                    data-title="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                    onclick="shareArticle('facebook', this.dataset.url, this.dataset.title)">
                                <span class="icon">📘</span>
                                Facebook
                            </button>
                            <button class="share-btn btn btn--secondary btn--sm"
                                    data-url="<?php echo SITE_URL; ?>/post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
                                    onclick="copyToClipboard(this.dataset.url)">
                                <span class="icon">🔗</span>
                                Copy Link
                            </button>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between items-center">
                        <a href="index.php" class="btn btn--ghost btn--sm">
                            <span class="icon">←</span>
                            Back to Home
                        </a>

                        <div class="flex gap-sm">
                            <button class="theme-toggle btn btn--ghost btn--sm" aria-label="Toggle dark mode">
                                <span class="theme-icon">🌙</span>
                            </button>
                        </div>
                    </div>
                </footer>
            </div>
        </article>
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

                <div class="flex flex-wrap justify-center gap-md mb-lg">
                    <a href="https://github.com/renggap/QiBlog"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn--ghost btn--sm">
                        <span class="icon">📖</span>
                        Documentation
                    </a>
                    <a href="admin/login.php"
                       class="btn btn--ghost btn--sm">
                        <span class="icon">⚙️</span>
                        Admin Panel
                    </a>
                    <a href="sitemap.xml"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn--ghost btn--sm">
                        <span class="icon">🗺️</span>
                        Sitemap
                    </a>
                </div>

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

        // Social Sharing
        function shareArticle(platform, url, title) {
            const encodedUrl = encodeURIComponent(url);
            const encodedTitle = encodeURIComponent(title);

            let shareUrl = '';

            switch (platform) {
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
                    break;
            }

            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        }

        function copyToClipboard(url) {
            navigator.clipboard.writeText(url).then(() => {
                // Show success feedback
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-success text-inverse px-lg py-md rounded-lg shadow-lg z-50 animate-fade-in-up';
                notification.textContent = 'Link copied to clipboard!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
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