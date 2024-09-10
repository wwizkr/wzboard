<h1 class="sound-only">사이트 제목</h1>
<header id="header">
    <div class="header-inner" style="max-width:1400px;">
        <div class="header-logo">
            <div class="logo"><a href="/"><?= $this->container->get('config_domain')['cf_title']; ?></a></div>
        </div>
        <div class="header-gnb"><?= $menu; ?></div>
        <div class="header-snb">
            <div class="header-search"></div>
            <div class="header-ico">
                <?php if($isLogin) { ?>
                <a href="/member/mypage">마이페이지</a>
                <a href="/auth/logout">로그아웃</a>
                <?php } else { ?>
                <a href="/member/register">회원가입</a>
                <a href="/auth/login">로그인</a>
                <?php } ?>
            </div>
        </div>
    </div>
</header>
<main style="max-width:<?= $this->container->get('config_domain')['cf_max_width']; ?>px;">