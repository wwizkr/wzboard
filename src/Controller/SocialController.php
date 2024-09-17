<?php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\SessionManager;
use Web\PublicHtml\Helper\CryptoHelper;
use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception;
use Web\PublicHtml\Model\MembersModel;

class SocialController
{
    private $container;
    private $config_domain;
    private $config;
    private $sessionManager;
    private $membersModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->sessionManager = $this->container->get('SessionManager');
        $this->membersModel = $this->container->get('MembersModel');
    }

    public function getSocialConfig()
    {
        $callbackUrl = $_ENV['APP_URL'].'/social/callback?provider=';
        $providers = $this->config_domain['cf_social_servicelist'] ? explode(",",$this->config_domain['cf_social_servicelist']) : [];
        $providers = ['naver']; // 임시
        $socialConfig = [];
        
        $provider = [];
        foreach($providers as $key=>$val) {
            if ($val === 'naver') {
                $provider[ucfirst(strtolower($val))] = [
                    'enabled' => true,
                    'keys' => [
                        'id' => $this->config_domain['cf_'.$val.'_clientid'],
                        'secret' => $this->config_domain['cf_'.$val.'_secret'],
                    ],
                    'callback' => $callbackUrl.ucfirst(strtolower($val)),
                    'wrapper' => [
                        'path' => '/home/web/public_html/vendor/hybridauth/hybridauth/src/Provider/Naver.php',
                        'class' => 'Hybridauth\\Provider\\Naver',
                    ],
                ];
            } else if($val !== 'kakao') {
                $provider[ucfirst(strtolower($val))] = [
                    'enabled' => true,
                    'keys' => [
                        'id' => $this->config_domain['cf_'.$val.'_clientid'],
                        'secret' => $this->config_domain['cf_'.$val.'_secret'],
                    ],
                    'callback' => $callbackUrl.ucfirst(strtolower($val)),
                ];
            } else {
                $provider[ucfirst(strtolower($val))] = [
                    'enabled' => true,
                    'keys' => [
                        'id' => $this->config_domain['cf_'.$val.'_clientid'],
                        'secret' => $this->config_domain['cf_'.$val.'_secret'],
                    ],
                    'callback' => $callbackUrl.ucfirst(strtolower($val)),
                ];
            }
        }
        $socialConfig['callback'] = '';
        $socialConfig['debug_mode'] = true;
        $socialConfig['debug_file'] = '/home/web/public_html/storage/social_log.log';
        $socialConfig['providers'] = $provider;

        return $socialConfig;
        
    }

    public function login($vars)
    {
        try {
            $providerName = $vars['param'] ?? 'Naver';
            $providerName = ucfirst(strtolower($providerName));
            if (empty($this->getSocialConfig()['providers'][$providerName])) {
                throw new Exception('Unknown Provider: ' . $providerName);
            }
            $hybridauth = new Hybridauth($this->getSocialConfig());
            $hybridauth->authenticate($providerName); // CallBack 리다이렉트
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
    
    /*
    object(Hybridauth\User\Profile)#69 (23) {
      ["identifier"]=> string(43) "ApgKT-t5Nlr2ZPTrki4raAj5jk0QgQzeyQ1t07ndPzk"
      ["webSiteURL"]=> NULL
      ["profileURL"]=> NULL
      ["photoURL"]=> NULL
      ["displayName"]=> string(9) "류지현"
      ["description"]=> NULL
      ["firstName"]=> NULL
      ["lastName"]=> NULL
      ["gender"]=> NULL
      ["language"]=> NULL
      ["age"]=> string(5) "40-49"
      ["birthDay"]=> NULL
      ["birthMonth"]=> NULL
      ["birthYear"]=> NULL
      ["email"]=> string(22) "made_in_king@naver.com"
      ["emailVerified"]=> NULL
      ["phone"]=> string(13) "010-8655-9999"
      ["address"]=> NULL
      ["country"]=> NULL
      ["region"]=> NULL
      ["city"]=> NULL
      ["zip"]=> NULL
      ["data"]=> array(0) { }
    */

    public function callback()
    {
        try {
            $hybridauth = new Hybridauth($this->getSocialConfig());
            
            $providerName = $_GET['provider'] ?? 'Naver';
            
            if (empty($providerName)) {
                throw new Exception('콜백 요청에 provider 이름이 누락되었습니다.');
            }
            $adapter = $hybridauth->authenticate($providerName);
            
            if ($adapter->isConnected()) {
                $userProfile = $adapter->getUserProfile();
                /*
                // 사용자가 이미 가입되어 있는지 확인
                $existingMember = $memberModel->findBySocialId($providerName, $userProfile->identifier);
                
                if ($existingMember) {
                    // 이미 가입된 회원이라면 로그인 처리
                    // ... 로그인 로직 ...
                    echo "로그인 성공: " . $userProfile->displayName;
                } else {
                    // 새 회원이라면 회원가입 페이지로 리다이렉트
                    $encryptedProfile = CryptoHelper::encryptJson((array)$userProfile);
                    $this->sessionManager->set('encrypted_social_profile', $encryptedProfile);
                    $this->sessionManager->set('social_provider', $providerName);
                    //header('Location: /member/register/join');
                    exit;
                }
                */
                
                $adapter->disconnect();
            } else {
                echo '사용자가 연결되지 않았습니다.';
            }
        } catch (Exception $e) {
            echo '콜백 처리 중 오류 발생: ' . $e->getMessage();
        }
    }

    /**
     * 제공자 목록을 가져오는 메소드
     */
    public function getProviderList()
    {
        $providers = $this->getSocialConfig()['providers'] ?? [];

        // 활성화된 제공자 목록만 반환
        $enabledProviders = [];
        foreach ($providers as $provider => $settings) {
            if (!empty($settings['enabled'])) {
                $enabledProviders[] = $provider;
            }
        }

        return $enabledProviders;
    }
}