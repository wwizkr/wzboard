<?php
//use Web\Admin\Helper\AdminMenuHelper;

//$menu = AdminMenuHelper::getAdminMenu();
?>
<!-- Navbar (상단) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
    <div class="container-fluid">
        <!-- 사이드 메뉴와 동일한 크기를 가지는 navbar-brand -->
        <a class="navbar-brand flex-shrink-0" href="#">Admin Panel</a>
        
        <!-- 상단 메뉴 -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav w-100">
                <?php foreach ($menu as $key => $item): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $item['url']; ?>"><?= $item['label']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- 오른쪽 메뉴와 토글 버튼 -->
        <div class="d-flex ms-auto align-items-center">
            <!-- 추가 메뉴들 -->
            <ul class="navbar-nav d-none d-lg-flex">
                <li class="nav-item">
                    <a class="nav-link" href="#">Extra Menu 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Extra Menu 2</a>
                </li>
            </ul>
            
            <!-- 모바일에서 보이는 토글 버튼 -->
            <button class="navbar-toggler ms-2" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</nav>

<!-- Main Layout -->
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar (사이드바) -->
        <div id="sidebar" class="col-auto col-md-3 col-lg-2 px-sm-2 bg-light">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline">Menu</span>
                </a>
                <ul class="nav flex-column">
                    <?php foreach ($menu as $key => $item): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="<?= isset($item['submenu']) ? '#' : $item['url']; ?>" <?= isset($item['submenu']) ? 'data-bs-toggle="collapse"' : ''; ?>>
                                <?php if (isset($item['icon'])): ?>
                                    <i class="<?= $item['icon']; ?> me-2"></i>
                                <?php endif; ?>
                                <?= $item['label']; ?>
                            </a>
                            <?php if (isset($item['submenu'])): ?>
                                <ul id="<?= $key; ?>Submenu" class="collapse">
                                    <?php foreach ($item['submenu'] as $subItem): ?>
                                        <li class="nav-item">
                                            <a class="nav-link ms-3" href="<?= $subItem['url']; ?>"><?= $subItem['label']; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
