<?php
require_once __DIR__ . '/../includes/functions.php';
start_session_if_needed();
unset($_SESSION['admin']);
header('Location: /admin/login.php');
exit;
