<?php

return [
    'callback' => 'http://yourdomain.com/public/callback.php', // 모든 SNS 로그인에 대한 공통 콜백 URL
    'providers' => [
        'Naver' => [
            'enabled' => true,
            'keys' => [
                'id' => 'YOUR_NAVER_CLIENT_ID',
                'secret' => 'YOUR_NAVER_CLIENT_SECRET'
            ]
        ],
        'Google' => [
            'enabled' => true,
            'keys' => [
                'id' => 'YOUR_GOOGLE_CLIENT_ID',
                'secret' => 'YOUR_GOOGLE_CLIENT_SECRET'
            ]
        ],
        'Facebook' => [
            'enabled' => true,
            'keys' => [
                'id' => 'YOUR_FACEBOOK_APP_ID',
                'secret' => 'YOUR_FACEBOOK_APP_SECRET'
            ]
        ],
        'Kakao' => [
            'enabled' => true,
            'keys' => [
                'id' => 'YOUR_KAKAO_CLIENT_ID',
                'secret' => 'YOUR_KAKAO_CLIENT_SECRET'
            ]
        ],
        // 필요한 추가 프로바이더 설정
    ]
];