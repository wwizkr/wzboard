<?php
namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\CryptoHelper;

class HomeController
{
    public function index()
    {
        // ��Ű���� JWT ��ū ��������
        $jwtToken = $_COOKIE['jwtToken'] ?? null;
        if ($jwtToken) {
            // ��ū ����
            $payload = CryptoHelper::verifyJwtToken($jwtToken);
            if ($payload) {
                // ��ū�� ��ȿ�ϸ� ����� ������ ó���� �� ����
                $viewData = [
                    'title' => 'Welcome to the Home Page',
                    'content' => 'This is the content of the home page.',
                    'mb_no' => $payload['mb_no'],
                    'mb_id' => $payload['mb_id'],
                    'nickName' => $payload['nickName'],
                ];
            } else {
                // ��ū�� ��ȿ���� ������ �α��� �������� �����̷�Ʈ
                header('Location: /auth/login');
                exit;
            }
        } else {
            // ��ū�� ������ �α��� �������� �����̷�Ʈ
            header('Location: /auth/login');
            exit;
        }

        // �� ��ο� �����͸� ��ȯ
        $skin = 'basic';
        return ["Home/{$skin}/index", $viewData];
    }

    public function create()
    {
        $skin = 'classic'; // �� �ٸ� ��Ų �̸�

        $viewData = [
            'title' => 'Welcome to the Home Page Create',
            'content' => 'This is the content of the home page create.'
        ];

        // �� ��ο� �����͸� ��ȯ, ��Ų ��� ����
        return ["Home/{$skin}/create", $viewData];
    }
}