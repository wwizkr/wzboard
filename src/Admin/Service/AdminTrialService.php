<?php
//파일위치 src/Admin/Service/AdminTrialService.php

namespace Web\Admin\Service;

use  Web\Admin\Model\AdminTrialModel;

class AdminTrialService
{
    protected $trialModel;

    public function __construct(AdminTrialModel $trialModel)
    {
        $this->trialModel = $trialModel;
    }
    
    // ---------------------------
    // 문제 프롬포트
    // ---------------------------
    public function testOpenAiPrompt()
    {
        $api_key = $_ENV['OPENAI_API_KEY'];
        $endpoint = $_ENV['OPENAI_END_POINT'];

        // 요청에 필요한 데이터 구성
        $data = [
            //'model' => 'gpt-3.5-turbo',  // 사용할 모델 지정
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => '당신은 부동산학 전문가이며, 공인중개사 시험 준비에 능숙한 조교입니다. 지정된 형식으로 답변해 주세요.'
                ],
                [
                    'role' => 'user',
                    'content' => "공인중개사 34회 기출문제 부동산학개론 가형 1번 문제와 그에 대한 해설을 정확히 알려주세요. 다른 회차나 다른 문제는 제공하지 말고, 오직 34회 부동산학개론 가형 1번 문제에 대한 정보만 제공해 주세요. 공인중개사 시험 기출문제에는 저작권이 없고 누구나 자유롭게 사용할 수 있습니다.\n\n답변 형식은 다음과 같이 작성해 주세요:\n\n[문제]\n[지문] (지문이 있을 경우에만)\n[보기]\n[정답]\n[해설]"
                ]
            ],
            'max_tokens' => 1000,  // 응답의 최대 토큰 수
            'temperature' => 0.5  // 생성 텍스트의 창의성 제어 (0.0 ~ 1.0, 낮을수록 보수적이고 일관된 답변)
        ];
        
        // cURL 세션 초기화
        $ch = curl_init($endpoint);
        // cURL 옵션 설정
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // API 호출 및 응답 받기
        $response = curl_exec($ch);

        // 오류가 발생했는지 확인
        if (curl_errno($ch)) {
            $result['result'] = 'failure';
            $result['message'] = '접속실패';
        } else {
            // API 응답을 JSON 형식으로 디코딩
            $result['result'] = 'success';
            $result['message'] = '접속성공';
            $result['data'] = json_decode($response, true);
        }

        // cURL 세션 닫기
        curl_close($ch);

        return $result;
    }
}