<?php
// 파일 위치: /src/Traits/TemplateItemDataTrait.php

namespace Web\PublicHtml\Traits;

trait TemplateItemDataTrait
{
    /**
     * 템플릿 아이템 데이터를 가져오고 처리합니다.
     * 
     * @param string $table 테이블 이름
     * @param string $itemType 아이템 타입
     * @param int $boxId 박스 ID
     * @param int $ctId CT ID
     * @return array 처리된 템플릿 아이템 데이터
     */
    public function getTemplateItemData(string $table, string $itemType, int $boxId, int $ctId): array
    {
        $skinDir = $this->getTemplateSkinDir($itemType);
        $data = [
            'result' => 'success',
            'message' => '템플릿 정보를 성공적으로 가져왔습니다.',
            'data' => [
                'items' => [],
                'useskin' => 'basic',
                'skinDir' => $skinDir,
                'display' => [],
            ]
        ];

        $listData = $this->adminTemplateModel->getTemplateDataById($table, $ctId, $this->config_domain['cf_id']);
        $useSkin = explode(",", $listData['ct_list_box_skin'] ?? '');
        $data['data']['useskin'] = $useSkin[$boxId] ?? 'basic';

        if ($itemType === 'file' && $data['data']['useskin'] === 'basic') {
            $data['data']['useskin'] .= '.php';
        }

        $boxData = $this->getTemplateCiBoxItem($table, $ctId, $boxId);

        switch($itemType) {
            case 'banner':
            case 'movie':
            case 'outlogin':
            case 'file':
                break;
            case 'image':
                $data['data']['items'] = $this->processImageData($table, $ctId, $boxId);
                break;
            case 'editor':
                $data['data']['items'] = $boxData;
                break;
            case 'board':
                $data['data']['items'] = $this->getTemplateBoardItem($ctId, $boxId);
                break;
            case 'boardgroup':
                $data['data']['items'] = $this->getTemplateBoardItem($ctId, $boxId);
                $data['data']['display'] = $boxData;
                break;
            default:
                return [
                    'result' => 'failure',
                    'message' => '템플릿 정보가 잘못되었습니다.',
                    'data' => []
                ];
        }

        return $data;
    }

    /**
     * 이미지 데이터를 가져오고 처리합니다.
     * 
     * @param string $table 테이블 이름
     * @param int $ctId CT ID
     * @param int $boxId 박스 ID
     * @return array 처리된 이미지 데이터
     */
    private function processImageData(string $table, int $ctId, int $boxId): array
    {
        $images = $this->getTemplateCiBoxItem($table, $ctId, $boxId);
        $processedImages = [];

        foreach($images as $key => $image) {
            $processedImages[$key] = $this->processImageItem($table, $image);
        }

        return $processedImages;
    }

    /**
     * 개별 이미지 아이템을 처리합니다.
     * 
     * @param array $image 원본 이미지 데이터
     * @return array 처리된 이미지 데이터
     */
    private function processImageItem(string $table, array $image): array
    {
        return [
            'pc_image_name' => $image['ci_pc_item'] ?? '',
            'mo_image_name' => $image['ci_mo_item'] ?? '',
            'pc_image_url' => $image['ci_pc_item'] ? '/storage/template/'.$this->config_domain['cf_id'].'/'.$table.'/'.$image['ci_pc_item'] : '',
            'mo_image_url' => $image['ci_mo_item'] ? '/storage/template/'.$this->config_domain['cf_id'].'/'.$table.'/'.$image['ci_mo_item'] : '',
            'link_url' => $image['ci_link'] ?? '',
            'link_win' => $image['ci_new_win'] ?? '',
        ];
    }

    /**
     * 템플릿 CI 박스 아이템을 가져옵니다.
     * 
     * @param string $table 테이블 이름
     * @param int $ctId CT ID
     * @param int $boxId 박스 ID
     * @return array 템플릿 CI 박스 아이템 데이터
     */
    public function getTemplateCiBoxItem(string $table, int $ctId, int $boxId): array
    {
        return $this->adminTemplateModel->getTemplateCiBoxItem($table, $ctId, $boxId, $this->config_domain['cf_id']);
    }

    /**
     * 템플릿 게시판 아이템을 가져옵니다.
     * 
     * @param int $ctId CT ID
     * @param int $boxId 박스 ID
     * @return array 템플릿 게시판 아이템 데이터
     */
    public function getTemplateBoardItem(int $ctId, int $boxId): array
    {
        $data = [];
        $adminBoardsService = $this->container->get('AdminBoardsService');
        $boardList = $adminBoardsService->getBoardsConfig(null);

        if (!empty($boardList)) {
            foreach($boardList as $key => $val) {
                $data[$key] = [
                    'board_no' => $val['no'],
                    'board_id' => $val['board_id'],
                    'board_name' => $val['board_name']
                ];
            }
        }

        return $data;
    }
}