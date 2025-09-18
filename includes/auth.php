<?php
require_once 'functions.php';

if (!is_logged_in() && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit;
}
?>