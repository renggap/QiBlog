<?php
require_once 'includes/functions.php';

$page = (int)($_GET['page'] ?? 1);

// Get all posts for homepage
$result = get_posts($page, 9); // Show 9 posts per page (3x3 grid)
$posts = $result['posts'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags(); ?>
    <title><?php echo htmlspecialchars(SITE_TITLE . " - Home", ENT_QUOTES, 'UTF-8'); ?></title>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
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

        h1 {
            color: white;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .blog-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #1f2937;
        }

        .card-title a {
            text-decoration: none;
            color: inherit;
        }

        .card-title a:hover {
            color: #4f46e5;
        }

        .card-description {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            color: #9ca3af;
        }

        .card-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-tags {
            display: flex;
            gap: 0.5rem;
        }

        .tag {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            color: #6b7280;
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

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 3rem;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            backdrop-filter: blur(10px);
            text-decoration: none;
        }

        .page-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .page-btn.active {
            background: white;
            color: #4f46e5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            h1 {
                font-size: 2rem;
            }

            .blog-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
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

        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state h2 {
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .empty-state a {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .empty-state a:hover {
            background: #4338ca;
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
            <div class="logo">
                <div class="logo-icon">B</div>
                <span style="color: white; font-size: 1.5rem; font-weight: 600;"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <p class="subtitle">Latest insights, tutorials, and resources for developers</p>
        </header>

        <?php if (empty($posts)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📝</div>
                <h2>No posts yet</h2>
                <p>Be the first to share your thoughts and ideas with the world.</p>
                <a href="admin/login.php">
                    <span style="margin-right: 0.5rem;">🚀</span>
                    Create Your First Post
                </a>
            </div>
        <?php else: ?>
            <!-- Posts Grid -->
            <div class="blog-grid">
                <?php
                $colors = ['purple', 'blue', 'green', 'orange', 'red', 'dark', 'pink', 'cyan', 'violet', 'indigo'];
                foreach ($posts as $index => $post):
                    $color = $colors[$index % count($colors)];
                    $category = !empty($post['categories']) ? htmlspecialchars($post['categories'][0], ENT_QUOTES, 'UTF-8') : 'General';
                ?>
                    <div class="blog-card">
                        <div class="card-header <?php echo $color; ?>">
                            <span><?php echo $category; ?></span>
                            <div class="check-icon">✓</div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">
                                <a href="<?php echo urlencode($post['categories'][0] ?? ''); ?>/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html">
                                    <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>
                            <p class="card-description">
                                <?php echo htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <div class="card-meta">
                                <span class="card-date">📅 <?php echo htmlspecialchars(date('M j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                <div class="card-tags">
                                    <?php if (!empty($post['categories'])): ?>
                                        <?php foreach (array_slice($post['categories'], 0, 2) as $cat): ?>
                                            <span class="tag"><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($result['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-btn">← Previous</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($result['total_pages'], $page + 2);

                    if ($start_page > 1): ?>
                        <a href="?page=1" class="page-btn">1</a>
                        <?php if ($start_page > 2): ?>
                            <span style="color: rgba(255, 255, 255, 0.7);">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($end_page < $result['total_pages']): ?>
                        <?php if ($end_page < $result['total_pages'] - 1): ?>
                            <span style="color: rgba(255, 255, 255, 0.7);">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $result['total_pages']; ?>" class="page-btn">
                            <?php echo $result['total_pages']; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($page < $result['total_pages']): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-btn">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Add interactive hover effects
        document.querySelectorAll('.blog-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });

            // Add click handler
            card.addEventListener('click', function() {
                const link = this.querySelector('.card-title a');
                if (link) {
                    window.location.href = link.href;
                }
            });
        });

        // Pagination functionality
        document.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Let the default link behavior handle the navigation
            });
        });

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
    </script>
</body>
</html>