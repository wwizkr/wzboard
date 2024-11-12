<?php

namespace Web\PublicHtml\Api\v1;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;

use Web\PublicHtml\Service\NaverShopService;

class NaverShopController
{
    protected $container;
    protected $config_domain;
    protected $membersModel;
    protected $membersService;
    protected $membersHelper;
    protected $authMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->membersModel = $this->container->get('MembersModel');
        $this->membersService = $this->container->get('MembersService');
        $this->membersHelper = $this->container->get('MembersHelper');

        $this->authMiddleware = $this->container->get('AuthMiddleware');
    }

    public function searchRank()
    {
        $inputData = file_get_contents('php://input');

        $data = json_decode($inputData, true);

        if (!$data['storeName']) {
            return [
                'result' => 'failure',
                'message' => '쇼핑몰명은 필수입니다.',
            ];
        }

        if (!$data['itemCode']) {
            return [
                'result' => 'failure',
                'message' => '상품코드는 필수입니다.',
            ];
        }

        if (!$data['searchKeyword']) {
            return [
                'result' => 'failure',
                'message' => '검색어는 필수입니다.',
            ];
        }

        // 데이터 유효성 검사
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return [
                'result' => 'failure',
                'message' => '잘못된 요청 데이터입니다.',
            ];
        }

        //error_log("Server::".print_r($_SERVER, true));
        
        $naverShopService = new NaverShopService($this->container);

        $result = $naverShopService->searchRank($data);

        return [
            'result' => 'success',
            'message' => '요청성공',
            'data' => [
                'rank' => $result,
            ],
        ];
    }
}