<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? $title : 'Default Title'; ?></title>
<link href="/assets/js/lib/swiper/swiper-bundle.min.css" rel="stylesheet">
<script src="/assets/js/lib/swiper/swiper-bundle.min.js"></script>
<link href="/assets/basic/css/common.css?<?=time();?>" rel="stylesheet">
<link href="/assets/basic/css/common-custom.css?<?=time();?>" rel="stylesheet">
<link href="/assets/basic/css/style.css?<?=time();?>" rel="stylesheet">
<link href="/assets/css/components/button.css?<?=time();?>" rel="stylesheet">
<?php
foreach ($this->getAssets('css') as $cssFile) {
    echo '<link href="' . htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') . '?'.time().'" rel="stylesheet">' . PHP_EOL;
}
?>
<script src="/assets/js/common.js?<?=time();?>"></script>
<script src="/assets/js/ajax.js?<?=time();?>"></script>
<script>
window.API_FULL_BASE_URL = '<?php echo $_ENV['API_FULL_BASE_URL'] ?? '/api/v1'; ?>';
</script>
</head>
<body>