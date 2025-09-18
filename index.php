<?php
require_once 'includes/functions.php';

$page = (int)($_GET['page'] ?? 1);
$result = get_posts($page, 15); // Show 15 posts per page
$posts = $result['posts'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags(); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>
<body>
    <header class="bg-dark text-white text-center py-5 mb-4">
        <div class="container">
            <h1 class="fw-light"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead">Latest Posts</p>
        </div>
    </header>

    <main class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title"><a href="post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html" class="text-decoration-none"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
                            <p class="card-text"><?php echo htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?> | Categories: <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?> | Tags: <?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?></small></p>
                            <a href="post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($posts)): ?>
                    <div class="text-center">
                        <p>No posts yet. <a href="admin/login.php">Login</a> to create your first post.</p>
                    </div>
                <?php endif; ?>

                <?php if ($result['total_pages'] > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $result['total_pages']): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Powered by Flat-File CMS</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>