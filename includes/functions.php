<?php
require_once 'Parsedown.php';
require_once 'config.php';

session_start();

// Authentication functions
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function login($password) {
    if (password_verify($password, ADMIN_PASSWORD)) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
}

// CSRF protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitization
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitize_slug($slug) {
    // Remove any characters that are not alphanumeric, hyphen, or underscore
    // Also remove any leading/trailing slashes or dots that could indicate directory traversal
    $slug = preg_replace('/[^A-Za-z0-9\-_]/', '', $slug);
    $slug = trim($slug, './'); // Remove leading/trailing dots or slashes
    return $slug;
}

// Post functions
function get_posts($page = 1, $per_page = null) {
    $all_files = glob(POSTS_DIR . '*' . POSTS_EXT);
    rsort($all_files); // Newest first
    $total = count($all_files);

    if ($per_page === null) {
        $files = $all_files;
        $total_pages = 1;
        $per_page = $total;
    } else {
        $offset = ($page - 1) * $per_page;
        $files = array_slice($all_files, $offset, $per_page);
        $total_pages = ceil($total / $per_page);
    }

    $posts = [];
    foreach ($files as $file) {
        $post = parse_post($file);
        if ($post) {
            $posts[] = $post;
        }
    }

    return [
        'posts' => $posts,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => $total_pages
    ];
}

function get_post_by_slug($slug) {
    $sanitized_slug = sanitize_slug($slug);
    $file = POSTS_DIR . $sanitized_slug . POSTS_EXT;
    if (file_exists($file)) {
        return parse_post($file);
    }
    return null;
}

function get_posts_by_category($category, $page = 1, $per_page = null) {
    $all_files = glob(POSTS_DIR . '*' . POSTS_EXT);
    rsort($all_files); // Newest first

    // Filter posts by category
    $filtered_files = [];
    foreach ($all_files as $file) {
        $post = parse_post($file);
        if ($post && in_array($category, $post['categories'])) {
            $filtered_files[] = $file;
        }
    }

    $total = count($filtered_files);

    if ($per_page === null) {
        $files = $filtered_files;
        $total_pages = 1;
        $per_page = $total;
    } else {
        $offset = ($page - 1) * $per_page;
        $files = array_slice($filtered_files, $offset, $per_page);
        $total_pages = ceil($total / $per_page);
    }

    $posts = [];
    foreach ($files as $file) {
        $post = parse_post($file);
        if ($post) {
            $posts[] = $post;
        }
    }

    return [
        'posts' => $posts,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => $total_pages,
        'category' => $category
    ];
}

function parse_post($file) {
    $content = file_get_contents($file);
    if (!$content) return null;

    $parts = explode('###', $content, 3);
    if (count($parts) < 3) return null;

    $frontmatter = yaml_parse($parts[1]);
    $body = $parts[2];

    return [
        'title' => $frontmatter['title'] ?? 'Untitled',
        'slug' => $frontmatter['slug'] ?? basename($file, POSTS_EXT),
        'date' => $frontmatter['date'] ?? date('Y-m-d'),
        'categories' => $frontmatter['categories'] ?? [],
        'tags' => $frontmatter['tags'] ?? [],
        'excerpt' => $frontmatter['excerpt'] ?? substr(strip_tags($body), 0, 150) . '...',
        'content' => $body,
        'file' => $file,
        'modified' => filemtime($file)
    ];
}

function save_post($data) {
    if (empty(trim($data['title']))) {
        return false;
    }

    $frontmatter = [
        'title' => sanitize_input($data['title']),
        'slug' => create_slug($data['title']),
        'date' => $data['date'] ?? date('Y-m-d H:i:s'),
        'categories' => array_map('sanitize_input', $data['categories'] ?? []),
        'tags' => array_map('sanitize_input', $data['tags'] ?? [])
    ];

    $content = "###\n" . yaml_emit($frontmatter) . "###\n" . $data['content'];

    $file = POSTS_DIR . $frontmatter['slug'] . POSTS_EXT;
    return file_put_contents($file, $content) !== false;
}

function delete_post($slug) {
    $sanitized_slug = sanitize_slug($slug);
    $file = POSTS_DIR . $sanitized_slug . POSTS_EXT;
    if (file_exists($file)) {
        return unlink($file);
    }
    return false;
}

function create_slug($title) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
}


// YAML functions (simple implementation if not available)
if (!function_exists('yaml_parse')) {
    function yaml_parse($yaml) {
        $data = [];
        $lines = explode("\n", trim($yaml));
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ($value === '') {
                    $data[$key] = [];
                } elseif (strpos($value, '[') === 0) {
                    $data[$key] = array_map('trim', explode(',', trim($value, '[]')));
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }
}

if (!function_exists('yaml_emit')) {
    function yaml_emit($data) {
        $yaml = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $yaml .= $key . ': [' . implode(', ', $value) . "]\n";
            } else {
                $yaml .= $key . ': ' . $value . "\n";
            }
        }
        return $yaml;
    }
}

// SEO functions
function generate_meta_tags($post = null, $category = null) {
    if ($category) {
        // Category page meta tags
        $title = htmlspecialchars(ucfirst($category) . ' | ' . SITE_TITLE, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars('Browse all posts in the ' . $category . ' category on ' . SITE_TITLE, ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars(SITE_URL . '/' . urlencode($category) . '/', ENT_QUOTES, 'UTF-8');
        $image = htmlspecialchars(SITE_URL . '/assets/images/default.jpg', ENT_QUOTES, 'UTF-8');

        $meta = '<title>' . $title . '</title>' . "\n";
        $meta .= '<meta name="description" content="' . $description . '">' . "\n";
        $meta .= '<meta name="keywords" content="' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        $meta .= '<link rel="canonical" href="' . $url . '">' . "\n";

        // Open Graph
        $meta .= '<meta property="og:title" content="' . $title . '">' . "\n";
        $meta .= '<meta property="og:description" content="' . $description . '">' . "\n";
        $meta .= '<meta property="og:url" content="' . $url . '">' . "\n";
        $meta .= '<meta property="og:type" content="website">' . "\n";
        $meta .= '<meta property="og:image" content="' . $image . '">' . "\n";

        // Twitter
        $meta .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $meta .= '<meta name="twitter:title" content="' . $title . '">' . "\n";
        $meta .= '<meta name="twitter:description" content="' . $description . '">' . "\n";
        $meta .= '<meta name="twitter:image" content="' . $image . '">' . "\n";
    } else {
        // Post or homepage meta tags
        $title = $post ? htmlspecialchars($post['title'] . ' | ' . SITE_TITLE, ENT_QUOTES, 'UTF-8') : htmlspecialchars(SITE_TITLE, ENT_QUOTES, 'UTF-8');
        $description = $post ? htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8') : htmlspecialchars('Jendela Info Blog', ENT_QUOTES, 'UTF-8');
        $url = $post ? htmlspecialchars(SITE_URL . '/' . urlencode($post['categories'][0] ?? '') . '/' . $post['slug'] . '.html', ENT_QUOTES, 'UTF-8') : htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8');
        $image = htmlspecialchars(SITE_URL . '/assets/images/default.jpg', ENT_QUOTES, 'UTF-8');

        $meta = '<title>' . $title . '</title>' . "\n";
        $meta .= '<meta name="description" content="' . $description . '">' . "\n";
        $meta .= '<meta name="keywords" content="' . htmlspecialchars(implode(', ', $post['categories'] ?? []), ENT_QUOTES, 'UTF-8') . '">' . "\n";
        $meta .= '<link rel="canonical" href="' . $url . '">' . "\n";

        // Open Graph
        $meta .= '<meta property="og:title" content="' . $title . '">' . "\n";
        $meta .= '<meta property="og:description" content="' . $description . '">' . "\n";
        $meta .= '<meta property="og:url" content="' . $url . '">' . "\n";
        $meta .= '<meta property="og:type" content="' . ($post ? 'article' : 'website') . '">' . "\n";
        $meta .= '<meta property="og:image" content="' . $image . '">' . "\n";

        // Twitter
        $meta .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $meta .= '<meta name="twitter:title" content="' . $title . '">' . "\n";
        $meta .= '<meta name="twitter:description" content="' . $description . '">' . "\n";
        $meta .= '<meta name="twitter:image" content="' . $image . '">' . "\n";
    }

    return $meta;
}

function generate_sitemap() {
    $result = get_posts();
    $posts = $result['posts'];
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $sitemap .= '<url><loc>' . SITE_URL . '</loc><lastmod>' . date('Y-m-d') . '</lastmod></url>' . "\n";

    foreach ($posts as $post) {
        if (!isset($post['slug']) || empty($post['slug'])) continue;
        $sitemap .= '<url><loc>' . SITE_URL . '/' . urlencode($post['categories'][0] ?? '') . '/' . $post['slug'] . '.html</loc><lastmod>' . date('Y-m-d', $post['modified']) . '</lastmod></url>' . "\n";
    }

    $sitemap .= '</urlset>';
    return $sitemap;
}

?>