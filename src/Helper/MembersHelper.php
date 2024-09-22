<?php
// 파일 위치: /src/Helper/MembersHelper.php

namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Model\MembersModel;

class MembersHelper
{
    protected $container;
    protected $membersModel; // 회원 관련 모델 인스턴스
    protected $sessionManager; // 세션 관리자 인스턴스

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->membersModel = $this->container->get('MembersModel');
        $this->sessionManager = $this->container->get('SessionManager');
    }

    
}