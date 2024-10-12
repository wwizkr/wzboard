<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="keyword" content="<?= $seoKeyword; ?>">
<meta name="description" content="<?= $seoDescription; ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= $seoKeyword; ?>">
<meta property="og:description" content="<?= $seoDescription; ?>">
<meta property="og:url" content="<?= $canonical; ?>">
<meta property="og:site_name" content="<?= $seoTitle; ?>">
<meta property="og:image" content="<?= $ogImage; ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $seoTitle; ?>">
<meta name="twitter:description" content="<?= $seoDescription; ?>">
<meta name="twitter:image" content="<?= $ogImage; ?>">
<?php
foreach($addMeta as $meta) {
    echo $meta.PHP_EOL;
}
?>
<title><?= $seoTitle; ?></title>
<link rel="canonical" href="<?= $canonical; ?>">
<link rel="icon" href="<?= $icoImage; ?>" type="image/x-icon">
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
<style>
<?php if ($config_domain['cf_content_max_width'] && $config_domain['cf_content_max_width'] > 100) { ?>
.max-layout { width: 100%; max-width: <?= $config_domain['cf_content_max_width']; ?>px; margin: 0 auto; }
<?php } else { ?>
.max-layout { width: 100%; max-width: 100%; margin: 0 auto; }
<?php } ?>
</style>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "name": "<?= $seoTitle; ?>",
  "url": "<?= $canonical; ?>"
}
</script>
<script src="/assets/js/common.js?<?=time();?>"></script>
<script src="/assets/js/ajax.js?<?=time();?>"></script>
<script>
window.API_FULL_BASE_URL = '<?php echo $_ENV['API_FULL_BASE_URL'] ?? '/api/v1'; ?>';
</script>
</head>
<body>