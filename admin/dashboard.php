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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8'); ?> Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="create.php">Create Post</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Dashboard</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="create.php" class="btn btn-primary">Create New Post</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Categories</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $post['categories']), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="edit.php?slug=<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?page=<?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?>&delete=<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            <a href="../post/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>.html" class="btn btn-sm btn-info" target="_blank">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($result['total_pages'] > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $result['total_pages']): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</body>
</html>