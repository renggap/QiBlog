# QiBlog: Flat-File CMS for Blogging

A simple, secure flat-file CMS built with PHP for blogging with WYSIWYG editor and SEO features.

## Features

- **Flat-File Storage**: Posts stored as HTML static file
- **WYSIWYG Editor**: CKEditor 5 for rich HTML editing
- **Authentication**: Session-based admin login
- **SEO Optimized**: Friendly URLs, XML sitemap, meta tags, structured data
- **Security**: CSRF protection, input sanitization, XSS prevention
- **Categories & Tags**: Organize posts with categories and tags

## Installation

1. **Install PHP**: Ensure PHP 7.4+ is installed on your server.

2. **Clone/Download**: Place files in your web directory.

3. **Permissions**: Ensure `posts/` directory is writable by PHP.

4. **Configuration**: Set the `ADMIN_PASSWORD` environment variable for secure admin login. Refer to your web server documentation for setting environment variables (e.g., Apache `SetEnv`, Nginx `fastcgi_param`). For local development, a fallback password 'admin123' is used, but this is INSECURE for production.

## Usage

### Admin Access
- Visit `/admin/login.php`
- Login with the password configured via `ADMIN_PASSWORD` environment variable.

### Creating Posts
- Login to admin
- Use "Create Post" to add new content
- CKEditor for formatting

### Viewing Posts
- Homepage: `index.php` shows latest posts
- Individual posts: `/post/slug`
- Sitemap: `/sitemap.xml`

## File Structure

```
/ (root)
├── index.php          # Homepage
├── post.php           # Individual post view
├── sitemap.php        # XML sitemap
├── .htaccess          # URL rewriting
├── admin/             # Admin panel
│   ├── login.php
│   ├── dashboard.php
│   ├── create.php
│   ├── edit.php
│   └── logout.php
├── includes/          # Core files
│   ├── config.php
│   ├── functions.php
│   ├── auth.php
├── posts/             # Markdown post files
└── assets/            # CSS/JS/images
```

## Security Notes

- **Secure Admin Password:** Ensure the `ADMIN_PASSWORD` environment variable is set to a strong, unique password in production.
- Keep PHP updated
- Use HTTPS in production
- Regular backups of `posts/` directory

## Code Review Summary

This section summarizes the comprehensive code review performed, highlighting key findings and the implemented improvements across security, code quality, and architectural considerations.

### Security Improvements Implemented:

*   **Secure Admin Password Management:** The `ADMIN_PASSWORD` is no longer hardcoded. It is now retrieved from an environment variable, significantly reducing the risk of credential exposure.
*   **Comprehensive XSS Prevention:** `htmlspecialchars()` has been applied to all dynamic, user-generated content (titles, excerpts, categories, tags, messages) and `SITE_TITLE` across `index.php`, `post.php`, and all admin panel files (`admin/dashboard.php`, `admin/create.php`, `admin/edit.php`, `admin/login.php`) to prevent Cross-Site Scripting attacks.
*   **Correct HTML Content Rendering for CKEditor:** The issue where CKEditor's HTML output was being incorrectly escaped in `post.php` has been resolved. The content is now rendered directly using `strip_tags()` with a whitelist of safe HTML tags (`<h3><p><a><br><strong><em><ul><ol><li><blockquote><img>`), allowing proper display while maintaining a basic level of XSS protection.
*   **Robust Directory Traversal Prevention:** A `sanitize_slug()` function was introduced in `includes/functions.php` to strictly filter user-provided slugs. This function is now used in `get_post_by_slug()` and `delete_post()` to prevent directory traversal attacks when constructing file paths.
*   **Cleaned .htaccess Admin Protection:** A redundant and potentially misleading `<FilesMatch>` block in `.htaccess` that used `Require all granted` for the admin directory has been removed. Authentication is now solely handled by the PHP-based `auth.php`.

### Code Quality & Maintainability Enhancements:

*   **Centralized Slug Sanitization:** The new `sanitize_slug()` function promotes consistent and secure handling of slugs throughout the application.
*   **Consistent Output Escaping:** Standardized the use of `htmlspecialchars()` for all dynamic content output, improving code readability and security.

These changes collectively enhance the security, reliability, and maintainability of the QiBlog Flat-File CMS.
