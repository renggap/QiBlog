<?php
require_once 'includes/functions.php';

$post = null;
if (isset($_GET['slug'])) {
    $post = get_post_by_slug($_GET['slug']);
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo 'Post not found';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags($post); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>",
        "datePublished": "<?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?>",
        "author": {
            "@type": "Person",
            "name": "Admin"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?>"
        }
    }
    </script>
</head>
<body>
    <header class="bg-dark text-white text-center py-5 mb-4">
        <div class="container">
            <h1 class="fw-light"><a href="/index.php" class="text-white text-decoration-none"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></a></h1>
        </div>
    </header>

    <main class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article>
                    <h1><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="text-muted"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?> | Categories: <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?> | Tags: <?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="content">
                        <?php
                        // Define allowed HTML tags for content from CKEditor
                        $allowed_html_tags = '<h3><p><a><br><strong><em><ul><ol><li><blockquote><img>';
                        echo strip_tags($post['content'], $allowed_html_tags);
                        ?>
                    </div>
                </article>
                <a href="/index.php" class="btn btn-secondary mt-3">← Back to Home</a>
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