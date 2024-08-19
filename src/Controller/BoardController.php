<?php
namespace Web\PublicHtml\Controller;

class BoardController
{
    public function handle($vars)
    {
        $boardId = $vars['boardId'];
        $method = $vars['method'] ?? 'index';
        $param = $vars['param'] ?? null;

        // 게시판 ID와 메서드에 따라 동적으로 처리
        if (method_exists($this, $method)) {
            return $this->$method($boardId, $param);
        } else {
            echo 'Method not found';
        }
    }

    // 게시판 목록을 보여주는 메서드 예시
    public function list($boardId)
    {
        $viewData = [
            'title' => "Board {$boardId} List",
            'content' => "This is the list of board {$boardId}."
        ];

        return ["Board/list", $viewData];
    }

    // 게시판 글 보기 메서드 예시
    public function view($boardId, $postId)
    {
        $viewData = [
            'title' => "Viewing post #{$postId} on board {$boardId}",
            'content' => "This is the detail of post #{$postId} on board {$boardId}."
        ];

        return ["Board/view", $viewData];
    }

    // 그 외 다른 메서드들...
}