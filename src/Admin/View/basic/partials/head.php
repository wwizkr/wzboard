<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if ($sessionManager->getCsrfToken('admin_secure_key')): ?>
<meta name="admin-token" content="<?php echo htmlspecialchars($sessionManager->getCsrfToken('admin_secure_key')); ?>">
<?php endif; ?>
<title><?= isset($title) ? $title : 'Default Title'; ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
<link  rel="stylesheet" href="/assets/basic/css/admin-style.css">
<link  rel="stylesheet" href="/assets/basic/css/common-custom.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/common.js"></script>
<script src="/assets/js/admin-ajax.js"></script>
</head>
<body>