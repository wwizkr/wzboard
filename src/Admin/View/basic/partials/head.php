<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if ($csrfToken): ?>
<meta name="admin-token" content="<?= htmlspecialchars($csrfToken); ?>">
<?php endif; ?>
<title><?= isset($title) ? $title : 'Default Title'; ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/basic/css/admin-style.css?<?=time();?>">
<link rel="stylesheet" href="/assets/basic/css/common-custom.css?<?=time();?>">
<link rel="stylesheet" href="/assets/css/components/button.css?<?=time();?>">
<?php
foreach ($this->getAssets('css') as $cssFile) {
    echo '<link href="' . htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') . '?'.time().'" rel="stylesheet">' . PHP_EOL;
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/common.js"></script>
<script src="/assets/js/admin-ajax.js"></script>
<script src="/assets/js/admin.js"></script>
<script>
window.API_FULL_BASE_URL = '<?php echo $_ENV['API_FULL_BASE_URL'] ?? '/api/v1'; ?>';
</script>
</head>
<body>