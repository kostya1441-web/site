<?php
require_once __DIR__ . '/includes/admin_auth.php';
unset($_SESSION['admin_id']);
redirect('/admin/login.php');
