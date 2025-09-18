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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="hero is-dark is-bold">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="subtitle">Latest Posts</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-8">
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4">
                        <div class="card-content">
                            <h2 class="title is-4"><a href="post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
                            <p class="content"><?php echo htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="is-size-7 has-text-grey"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?> | Categories: <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?> | Tags: <?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html" class="button is-primary">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($posts)): ?>
                    <div class="has-text-centered">
                        <p>No posts yet. <a href="admin/login.php">Login</a> to create your first post.</p>
                    </div>
                <?php endif; ?>

                <?php if ($result['total_pages'] > 1): ?>
                    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                        <?php if ($page > 1): ?>
                            <a class="pagination-previous" href="?page=<?php echo $page - 1; ?>">Previous</a>
                        <?php endif; ?>

                        <?php if ($page < $result['total_pages']): ?>
                            <a class="pagination-next" href="?page=<?php echo $page + 1; ?>">Next</a>
                        <?php endif; ?>

                        <ul class="pagination-list">
                            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                                <li>
                                    <a class="pagination-link <?php echo $i == $page ? 'is-current' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="content has-text-centered">
            <p>Powered by <a href="https://github.com/renggap/QiBlog" target="_blank">QiBlog</a> Flat-File CMS</p>
        </div>
    </footer>

</body>
</html>