<?php
session_start();
require_once __DIR__ . '/config.php';
header('Location: ' . BASE_URL . 'frontend/pages/index.php');
exit;
