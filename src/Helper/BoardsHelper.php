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
    protected $boardsModel;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
        $this->sessionManager = $container->get('SessionManager');
        $this->cookieManager = $container->get('CookieManager');
        $this->boardsModel = $container->get('BoardsModel');
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
                ? '<div class="list-item list-comment">댓글 '.number_format($val['comment_count']).'</div>'
                : '';

            // CONTENT 에서 THUMBNAIL 생성.
            $thumb = CommonHelper::createThumbnailFromContent($val['content']) ?? '';
            $data[$key]['thumb'] = $thumb
                ? '<div class="list-col list-thumb"><img src="'.$thumb.'" title="'.$data[$key]['title'].'"></div>'
                : '';

            // 비밀번호 삭제
            unset($data[$key]['password']);
        }

        return $data;
    }

    public function processArticleViewData(array $boardConfig, array $articleData) : array
    {
        $data = [];

        $data = $articleData;
        $data['title'] = strip_tags($articleData['title']);
        $data['slug'] = strip_tags($articleData['slug']);
        $data['nickName'] = strip_tags($articleData['nickName']);
        $data['content'] = htmlspecialchars_decode($articleData['content']);

        // 날짜 포맷
        $data['date1'] = CommonHelper::formatTimeAgo($articleData['created_at']); //몇초전, 몇시간전...
        $data['date2'] = substr($articleData['created_at'],0,10); // 2024-09-01
        $data['date3'] = substr($articleData['created_at'],2,8); // 23-09-01
        $data['date4'] = substr($articleData['created_at'],0,16); // 23-09-01 12:19

        // 댓글갯수 출력
        $data['comment'] = number_format($articleData['comment_count']);

        // 비밀번호 삭제
        unset($data['password']);

        return $data;
    }

    public function processCommentData(array $boardConfig, array $commentData) : array
    {
        $data = [];

        $commentReaction = [];
        $reactionArray = $boardConfig['is_comment_reaction'] ? explode(",", $boardConfig['is_comment_reaction']) : [];
        foreach($reactionArray as $key=>$val) {
            $value = explode(":", $val);
            $commentReaction[$key]['field'] = $value[0];
            $commentReaction[$key]['text'] = $value[1];
        }

        foreach($commentData as $key=>$val) {
            $data[$key] = $val;
            
            $data[$key]['nickName'] = strip_tags($val['nickName']);

            // 날짜 포맷
            $data[$key]['date1'] = CommonHelper::formatTimeAgo($val['created_at']); //몇초전, 몇시간전...
            $data[$key]['date2'] = substr($val['created_at'],0,10); // 2024-09-01
            $data[$key]['date3'] = substr($val['created_at'],2,8); // 23-09-01
            $data[$key]['date4'] = substr($val['created_at'],0,16); // 23-09-01 12:19

            // 리액션.
            $reaction = '';
            if (!empty($commentReaction)) {
                foreach($commentReaction as $act) {
                    $reaction .= '<button type="button" class="btn btn-reaction '.$act['field'].'" data-table="comments" data-action="'.$act['field'].'" data-comment="'.$val['no'].'">';
                        $reaction .= '<span>'.$act['text'].'</span>';
                        $reaction .= '<span class="reaction-count">'.$val[$act['field'].'_count'].'</span>';
                    $reaction .= '</button>';
                }
            }

            $val['parentComment'] = [];
            if ($val['parent_no']) {
                $val['parentComment'] = $this->boardsModel->getCommentData($val['parent_no']);
            }
            
            if (!empty($val['parentComment'])) {
                $parentNickname = '<span class="parent-name">@'.$val['parentComment']['nickName'].'</span>';
                $updatedContent = preg_replace('/(<p[^>]*>)/i', '$1' . $parentNickname, $data[$key]['content'], 1);
                $data[$key]['content'] = $updatedContent;
            }

            $data[$key]['reaction'] = $reaction;

            // 비밀번호 삭제
            unset($data[$key]['password']);
        }

        return $data;
    }
}