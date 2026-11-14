<?php
declare(strict_types=1);

// The local-network photo manager has been removed from the admin interface.
// Keep this legacy URL safe for bookmarks and old links.
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}
header('Location: index.php');
exit;
