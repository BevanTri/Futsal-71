<?php
session_start();
require_once __DIR__ . '/../../config.php';
$_SESSION = [];
session_destroy();
header('Location: ' . BASE_URL . 'frontend/pages/index.php');
exit;
