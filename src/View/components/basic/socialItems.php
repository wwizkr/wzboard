<!-- components/skinname/socialItems.php -->
<div class="social-login-buttons">
    <?php foreach ($data as $provider): ?>
        <?php 
        // 소셜 로그인 URL 및 아이콘 설정
        $loginUrl = "/social/login/" . strtolower($provider); 
        //$iconPath = "/assets/icons/" . strtolower($provider) . ".png"; 
        ?>
        <!-- 각 소셜 로그인 버튼 -->
        <a href="<?php echo $loginUrl; ?>" class="btn btn-social btn-<?php echo strtolower($provider); ?>">
            <!---<img src="<?php echo $iconPath; ?>" alt="<?php echo $provider; ?> icon" class="sns-icon">-->
            <?php echo $provider; ?> 로그인
        </a>
    <?php endforeach; ?>
</div>