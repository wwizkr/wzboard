<?php
namespace Web\Admin\Controller;

class UserController
{
    public function index()
    {
        $viewData = [
            'title' => 'User List',
            'content' => 'This is the user list.'
        ];

        return ['User/index', $viewData];
    }

    public function list()
    {
        // 유저 리스트를 출력하는 코드
        $viewData = [
            'title' => 'User List',
            'content' => 'This is the user list.'
        ];

        return ['User/list', $viewData];
    }

    public function view($params)
    {
        // 특정 유저를 보는 코드
        $userId = $params['id'];
        $viewData = [
            'title' => "Viewing User #{$userId}",
            'content' => "This is the detail for user #{$userId}."
        ];

        return ['User/view', $viewData];
    }
}