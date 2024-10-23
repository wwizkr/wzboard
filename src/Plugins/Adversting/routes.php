<?php
return function(FastRoute\RouteCollector $r, array $httpMethods) {
    // 관리자 라우트 - 더 구체적인 패턴 사용
    $r->addRoute($httpMethods, '/adversting/admin/{method}[/{param}]', 'Plugins\\Adversting\\Controller\\AdminController');
    
    // 일반 라우트 - 'admin'을 제외한 모든 메소드에 대해 매칭
    $r->addRoute($httpMethods, '/adversting/{method}[/{param}]', 'Plugins\\Adversting\\Controller\\AdverstingController');
};