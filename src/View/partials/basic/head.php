<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? $title : 'Default Title'; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<link href="/assets/basic/css/common.css" rel="stylesheet">
<link href="/assets/basic/css/common-custom.css" rel="stylesheet">
<script src="/assets/js/common.js"></script>
<script src="/assets/js/ajax.js"></script>
<script>
window.API_FULL_BASE_URL = '<?php echo $_ENV['API_FULL_BASE_URL'] ?? '/api/v1'; ?>';
</script>
</head>
<body>