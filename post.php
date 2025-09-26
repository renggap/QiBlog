<?php
require_once 'includes/functions.php';

$post = null;
$category = $_GET['category'] ?? null;
$slug = $_GET['slug'] ?? null;

if ($slug) {
    $post = get_post_by_slug($slug);
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
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .error-container {
                background: white;
                border-radius: 16px;
                padding: 3rem;
                text-align: center;
                max-width: 600px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .error-container h1 {
                color: #1f2937;
                margin-bottom: 1rem;
                font-size: 2rem;
            }

            .error-container p {
                color: #6b7280;
                margin-bottom: 2rem;
            }

            .error-container a {
                display: inline-block;
                background: #4f46e5;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #4338ca;
            }

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
        </style>
    </head>
    <body>
        <div class="floating-elements">
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
        </div>
        
        <div class="error-container">
            <h1>404 - Post Not Found</h1>
            <p>The post you're looking for doesn't exist.</p>
            <a href="index.php">
                <span style="margin-right: 0.5rem;">🏠</span>
                Back to Home
            </a>
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
    <title><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
    
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
            "@id": "<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
        },
        "articleSection": "<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>",
        "keywords": "<?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?>"
    }
    </script>

    <!-- Breadcrumb Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?php echo SITE_URL; ?>/"
            }
            <?php if (!empty($post['categories'])): ?>
            <?php foreach ($post['categories'] as $index => $category): ?>
            ,{
                "@type": "ListItem",
                "position": <?php echo 2 + $index; ?>,
                "name": "<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>",
                "item": "<?php echo SITE_URL; ?>/<?php echo urlencode($category); ?>/"
            }
            <?php endforeach; ?>
            <?php endif; ?>
            ,{
                "@type": "ListItem",
                "position": <?php echo 2 + count($post['categories']); ?>,
                "name": "<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>",
                "item": "<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"
            }
        ]
    }
    </script>
    
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
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .post-container {
            max-width: 800px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #4f46e5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .post-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .card-header {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .card-content {
            padding: 2rem;
        }

        .post-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
            line-height: 1.2;
        }

        .post-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .post-content {
            color: #4b5563;
            margin-bottom: 2rem;
        }

        .post-content h1, .post-content h2, .post-content h3,
        .post-content h4, .post-content h5, .post-content h6 {
            color: #1f2937;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .post-content p {
            margin-bottom: 1rem;
        }

        .post-content ul, .post-content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .post-content li {
            margin-bottom: 0.5rem;
        }

        .post-content blockquote {
            border-left: 4px solid #4f46e5;
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #6b7280;
            font-style: italic;
        }

        .post-content pre {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.5rem 0;
        }

        .post-content code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .post-content pre code {
            background: none;
            padding: 0;
        }

        .post-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 2rem;
            margin-top: 2rem;
        }

        .share-section {
            margin-bottom: 2rem;
        }

        .share-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .share-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .share-btn {
            background: #f3f4f6;
            color: #4b5563;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .share-btn:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }

        /* Color variations for cards */
        .purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .red { background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); }
        .dark { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        .pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .violet { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .indigo { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

        .check-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumbs a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: white;
        }

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

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .post-title {
                font-size: 1.5rem;
            }

            .card-content {
                padding: 1.5rem;
            }

            .share-buttons {
                justify-content: center;
            }

            .navigation {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <div class="container">
        <header>
            <a href="/index.php" class="logo" style="text-decoration: none;">
                <div class="logo-icon">B</div>
                <span style="color: white; font-size: 1.5rem; font-weight: 600;"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        </header>

        <div class="post-container">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <a href="/index.php">Home</a>
                <span>→</span>
                <?php if (!empty($post['categories'])): ?>
                    <?php foreach ($post['categories'] as $index => $category): ?>
                        <?php if ($index > 0): ?>
                            <span>→</span>
                        <?php endif; ?>
                        <a href="/<?php echo urlencode($category); ?>/"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php endforeach; ?>
                    <span>→</span>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></span>
            </nav>

            <!-- Post Card -->
            <article class="post-card">
                <?php
                $colors = ['purple', 'blue', 'green', 'orange', 'red', 'dark', 'pink', 'cyan', 'violet', 'indigo'];
                $category = !empty($post['categories']) ? htmlspecialchars($post['categories'][0], ENT_QUOTES, 'UTF-8') : 'General';
                $colorIndex = crc32($category) % count($colors);
                $color = $colors[$colorIndex];
                ?>
                <div class="card-header <?php echo $color; ?>">
                    <span><?php echo $category; ?></span>
                    <div class="check-icon">✓</div>
                </div>
                <div class="card-content">
                    <h1 class="post-title"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    
                    <div class="post-meta">
                        <span>📅 <?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if (!empty($post['categories'])): ?>
                            <span>🏷️ <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="post-content">
                        <?php
                        // Define allowed HTML tags for content from CKEditor
                        $allowed_html_tags = '<h1><h2><h3><h4><h5><h6><p><a><br><strong><em><ul><ol><li><blockquote><img><pre><code><table><thead><tbody><tr><th><td><hr><figure><figcaption>';
                        echo strip_tags($post['content'], $allowed_html_tags);
                        ?>
                    </div>

                    <div class="post-footer">
                        <!-- Social Sharing -->
                        <div class="share-section">
                            <h3>Share this article</h3>
                            <div class="share-buttons">
                                <button class="share-btn" onclick="shareArticle('twitter', '<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html', '<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                    <span>🐦</span>
                                    Twitter
                                </button>
                                <button class="share-btn" onclick="shareArticle('facebook', '<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html', '<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                    <span>📘</span>
                                    Facebook
                                </button>
                                <button class="share-btn" onclick="copyToClipboard('<?php echo SITE_URL; ?>/<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html')">
                                    <span>🔗</span>
                                    Copy Link
                                </button>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="navigation">
                            <!-- Navigation removed as site title now links to homepage -->
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <script>
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
                notification.className = 'notification';
                notification.textContent = 'Link copied to clipboard!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            });
        }

        // Add parallax effect to floating elements
        let mouseX = 0, mouseY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX / window.innerWidth;
            mouseY = e.clientY / window.innerHeight;
            
            document.querySelectorAll('.floating-circle').forEach((circle, index) => {
                const speed = (index + 1) * 0.5;
                circle.style.transform = `translate(${mouseX * 20 * speed}px, ${mouseY * 20 * speed}px)`;
            });
        });

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
    </script>
</body>
</html>