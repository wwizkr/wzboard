<?php
// 파일 위치: /src/Admin/Controller/TrialadminController.php
// 문제 관리 컨트롤러
/*
 * Json 응답값
 * @param result = "success" : "failure"
 * @param message = "text"
 * @param gotoUrl = "url" 있을 경우 해당 URL로 이동
 * @param refresh = true 이면 새로 고침
 */

namespace Web\Admin\Controller;

use Web\Admin\Model\AdminTrialModel;
use Web\Admin\Service\AdminTrialService;
use Web\PublicHtml\Helper\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Middleware\FormDataMiddleware;
use Web\PublicHtml\Middleware\CsrfTokenHandler;

class TrialadminController
{
    protected $container;
    protected $configDomain;
    protected $trialModel;
    protected $trialService;
    protected $formDataMiddleware;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->configDomain = $container->get('config_domain');
        $this->trialModel = new AdminTrialModel($container);
        $this->trialService = new AdminTrialService($this->trialModel);

        // CsrfTokenHandler와 FormDataMiddleware 인스턴스 생성
        $csrfTokenHandler = new CsrfTokenHandler($container->get('session_manager'));
        $this->formDataMiddleware = new FormDataMiddleware($csrfTokenHandler);
    }

    // ---------------------------
    // 문제 프롬포트 메서드
    // ---------------------------

    public function prompt()
    {

        $result = $this->trialService->testOpenAiPrompt();

        $viewData = [
            'title' => '문제 프롬포트',
            'content' => '',
            'config_domain' => $this->configDomain,
            'resultData'=>$result,
        ];

        return ['TrialAdmin/prompt', $viewData];
    }

    
}