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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
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
    <section class="hero is-dark is-bold">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title"><a href="/index.php" class="has-text-white"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></a></h1>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-8">
                <article>
                    <h1 class="title"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="is-size-7 has-text-grey"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['date'])), ENT_QUOTES, 'UTF-8'); ?> | Categories: <?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?> | Tags: <?php echo htmlspecialchars(implode(', ', $post['tags']), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="content">
                        <?php
                        // Define allowed HTML tags for content from CKEditor
                        $allowed_html_tags = '<h3><p><a><br><strong><em><ul><ol><li><blockquote><img>';
                        echo strip_tags($post['content'], $allowed_html_tags);
                        ?>
                    </div>
                </article>
                <a href="/index.php" class="button is-light mt-3">← Back to Home</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="content has-text-centered">
            <p>Powered by Flat-File CMS</p>
        </div>
    </footer>

</body>
</html>