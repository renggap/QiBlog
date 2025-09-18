<?php
require_once 'includes/auth.php';
require_once '../includes/functions.php';

// Handle file upload for CKEditor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];

    // Check if file is an image and validate MIME type and extension
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($mime_type, $allowed_mime_types) || !in_array($ext, $allowed_extensions)) {
        http_response_code(400);
        echo json_encode(['error' => ['message' => 'Invalid file type or extension']]);
        exit;
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => ['message' => 'File too large']]);
        exit;
    }

    // Generate unique filename
    // $ext is already defined above
    $filename = uniqid() . '.' . $ext;
    $upload_path = '../assets/images/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Return success response for CKEditor
        echo json_encode([
            'url' => SITE_URL . '/assets/images/' . $filename
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => ['message' => 'Upload failed']]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'No file uploaded']]);
}
?>