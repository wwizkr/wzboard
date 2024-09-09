<header>
    <h1>사이트 제목</h1>
    <?php if (isset($menu)) { echo $menu; } ?>
    <?php
    $sessionManager = $this->container->get('session_manager');
    $authInfo = $sessionManager->get('auth');
    
    if (!empty($authInfo)) {
        echo '<a href="/auth/logout">로그아웃</a>';
    }
    ?>
</header>
<main>