<?php
require_once 'includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');
echo generate_sitemap();
?>