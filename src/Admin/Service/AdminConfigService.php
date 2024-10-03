<?php
//파일위치 src/Service/ConfigService.php

namespace Web\Admin\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Model\AdminConfigModel;
use Web\PublicHtml\Helper\CacheHelper;
use Web\PublicHtml\Helper\CryptoHelper;

//use Web\PublicHtml\Middleware\FormDataMiddleware;
//use Web\PublicHtml\Helper\MenuHelper;
//use Web\PublicHtml\Helper\ConfigHelper;


class AdminConfigService
{
    protected $container;
    protected $config_domain;
    protected $adminConfigModel;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->adminConfigModel = new AdminConfigModel($this->container);
        $this->formDataMiddleware = $this->container->get('FormDataMiddleware');
    }

    /**
     * 특정 cf_id에 해당하는 일반 설정을 가져옵니다.
     *
     * @param int $cf_id
     * @return array 설정 데이터
     */
    public function getConfigDomain($cf_id)
    {
        return $this->adminConfigModel->getConfigDomainByCfId($cf_id);
    }

    /**
     * 특정 cf_id에 해당하는 일반 설정을 업데이트합니다.
     *
     * @param int $cf_id
     * @param array $data 업데이트할 데이터
     * @return bool 업데이트 성공 여부
     */
    public function updateConfigDomain($cf_id, array $data)
    {
        /*
         * 데이터 업데이트 후 캐시 갱신
         */
        $result = $this->adminConfigModel->updateConfigDomainByCfId($cf_id, $data);

        $updated = $this->adminConfigModel->getConfigDomainByCfId($cf_id);

        $configCacheKey = 'config_domain_' . $updated['cf_domain'];

        $encryptedData = CryptoHelper::encryptJson($updated);
        CacheHelper::setCache($configCacheKey, $encryptedData);

        return $updated;
    }
}