<h1 class="sound-only">사이트 제목</h1>
<header id="header">
    <div class="header-inner" style="max-width:<?= $config_domain['cf_layout_max_width'] ?? '1200' ?>px;">
        <div class="header-logo">
            <div class="logo"><a href="/"><?= $config_domain['cf_title'] ?? 'Site Title' ?></a></div>
        </div>
        <div class="header-gnb swiper-container"><?= $menu ?? '' ?></div>
        <div class="header-snb">
            <div class="header-search"></div>
            <div class="header-ico">
                <?php if($isLogin ?? false): ?>
                <a href="/member/mypage">마이페이지</a>
                <a href="/auth/logout">로그아웃</a>
                <?php else: ?>
                <a href="/member/register">회원가입</a>
                <a href="/auth/login">로그인</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<script>
const menuSwiper = new Swiper('.header-gnb', {
    slidesPerView: 'auto',
    touchRatio: 1,
    observer: true,
    observeParents: true,
});
</script>
<?= $subContent ?? '' ?>
<main id="main" class="<?= $mainStyle ?? '' ?>">