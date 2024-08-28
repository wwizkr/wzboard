<?php
// 파일 위치: /src/Model/BoardModel.php

namespace Web\PublicHtml\Model;

use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;

class BoardsModel
{
    use DatabaseHelperTrait;

    protected $db;
    protected $configDomain;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->configDomain = $container->get('config_domain');
    }
}