<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\CryptoHelper;

class HomeController
{
    public function index()
    {
        // 쿠키에서 JWT 토큰 가져오기
        $jwtToken = $_COOKIE['jwtToken'] ?? null;
        if ($jwtToken) {
            // 토큰 검증
            $payload = CryptoHelper::verifyJwtToken($jwtToken);
            if ($payload) {
                // 토큰이 유효하면 사용자 정보를 처리할 수 있음
                $viewData = [
                    'title' => 'Welcome to the Home Page',
                    'content' => 'This is the content of the home page.',
                    'mb_no' => $payload['mb_no'],
                    'mb_id' => $payload['mb_id'],
                    'nickName' => $payload['nickName'],
                ];
            } else {
                // 토큰이 유효하지 않으면 로그인 페이지로 리다이렉트
                header('Location: /auth/login');
                exit;
            }
        } else {
            // 토큰이 없으면 로그인 페이지로 리다이렉트
            header('Location: /auth/login');
            exit;
        }

        // 뷰 경로와 데이터를 반환
        $skin = 'basic';
        return ["Home/{$skin}/index", $viewData];
    }

    public function create()
    {
        $skin = 'classic'; // 또 다른 스킨 이름

        $viewData = [
            'title' => 'Welcome to the Home Page Create',
            'content' => 'This is the content of the home page create.'
        ];

        // 뷰 경로와 데이터를 반환, 스킨 경로 포함
        return ["Home/{$skin}/create", $viewData];
    }
}