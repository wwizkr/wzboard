<?php
// 파일 위치: /src/Helper/BoardsHelper.php
namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;
//use Web\PublicHtml\Helper\ConfigHelper;
//use Web\PublicHtml\Helper\SessionManager;
//use Web\PublicHtml\Helper\CookieManager;

class BoardsHelper
{
    protected DependencyContainer $container;
    protected $config_domain;
    protected $sessionManager;
    protected $cookieManager;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->sessionManager = $container->get('SessionManager');
        $this->cookieManager = $container->get('CookieManager');
    }

    public function processArticleData(array $boardConfig, array $articleData) : array
    {
        $data = [];

        foreach($articleData as $key=>$val) {
            $data[$key] = $val;
            $data[$key]['title'] = strip_tags($val['title']);
            $data[$key]['slug'] = strip_tags($val['slug']);
            $data[$key]['nickName'] = strip_tags($val['nickName']);

            // 날짜 포맷
            $data[$key]['date1'] = CommonHelper::formatTimeAgo($val['created_at']); //몇초전, 몇시간전...
            $data[$key]['date2'] = substr($val['created_at'],0,10); // 2024-09-01
            $data[$key]['date3'] = substr($val['created_at'],2,8); // 23-09-01
            $data[$key]['date4'] = substr($val['created_at'],0,16); // 23-09-01 12:19

            // 댓글갯수 출력
            $data[$key]['comment'] = $boardConfig['is_use_comment']
                ? '<div class="list-item list-comment">'.number_format($val['comment_count']).'</div>'
                : '';

            // CONTENT 에서 THUMBNAIL 생성.
            $thumb = CommonHelper::createThumbnailFromContent($val['content']) ?? '';
            $data[$key]['thumb'] = $thumb
                ? '<div class="list-group-col list-thumb text-center" style="width:200px;"><img src="'.$thumb.'" title="'.$data[$key]['title'].'"></div>'
                : '';

            // 비밀번호 삭제
            unset($data[$key]['password']);
        }

        return $data;
    }
}