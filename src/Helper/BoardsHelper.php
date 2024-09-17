<?php
// 파일 위치: /src/Helper/BoardsHelper.php
namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;
use Web\Admin\Service\AdminBoardsService;
use Web\Admin\Helper\AdminBoardsHelper;
use Web\PublicHtml\Service\BoardsService;
use Web\PublicHtml\Model\BoardsModel;
use Web\PublicHtml\Helper\ConfigHelper;

class BoardsHelper
{
    protected DependencyContainer $container;
    protected $adminBoardsService;
    protected $boardsService;
    protected $boardsModel;
    protected $configHelper;
    protected $config_domain;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->adminBoardsService = $container->get('AdminBoardsService');
        $this->boardsModel = $container->get('BoardsModel');
        $this->configHelper = $container->get('ConfigHelper');
        $this->config_domain = $this->configHelper->getConfig('config_domain');
        // BoardsService는 나중에 필요할 때 가져오도록 합니다.
    }

    // getBoardsService 메서드 추가
    protected function getBoardsService()
    {
        if ($this->boardsService === null) {
            $this->boardsService = $this->container->get('BoardsService');
        }
        return $this->boardsService;
    }

    public function getGroupData()
    {
        return $this->adminBoardsService->getBoardsGroup(null);
    }

    public function getCategoryData($category_no = null)
    {
        return $this->adminBoardsService->getCategoryData($category_no);
    }

    public function getBoardsConfig($boardId = null)
    {
        return $this->adminBoardsService->getBoardsConfig($boardId);
    }

    public function getSkinData()
    {
        return AdminBoardsHelper::getBoardSkinDir();
    }

    public function getBoardsCategoryMapping($board_no)
    {
        return $this->adminBoardsService->getBoardsCategoryMapping($board_no);
    }
    
    /**
     * 게시판 권한 체크 메서드
     *
     * @param string $type 권한 체크 타입 (view, write, download, comment 등)
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 해당 타입의 권한이 있는지 여부
     */
    public function boardPermissionCheck($type, $boardConfig, $articleData, $memberData)
    {
        switch ($type) {
            case 'view':
                return $this->checkViewPermission($boardConfig, $articleData, $memberData);
            case 'write':
                return $this->checkWritePermission($boardConfig, $memberData);
            case 'download':
                return $this->checkDownloadPermission($boardConfig, $articleData, $memberData);
            case 'comment':
                return $this->checkCommentPermission($boardConfig, $articleData, $memberData);
            default:
                return false; // 기본적으로 권한 없음
        }
    }

    /**
     * 글 읽기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 읽기 권한이 있는지 여부
     */
    protected function checkViewPermission($boardConfig, $articleData, $memberData)
    {
        if ($this->isOwnArticle($articleData, $memberData)) {
            return true; // 자신의 글이면 조회수 증가 없이 return
        }

        if ($this->isGuestWithReadPermission($boardConfig, $articleData, $memberData)) {
            return true; // 비회원이 읽기 권한을 가지고 있으면 return
        }

        if ($this->isPublicArticle($boardConfig, $articleData)) {
            return $this->processView($articleData); // 공개 글이면 처리
        }

        if ($this->hasReadLevelPermission($boardConfig, $articleData, $memberData)) {
            return $this->processView($articleData, $memberData, $boardConfig); // 권한 있는 회원 처리
        }

        return false;
    }

    /**
     * 글 쓰기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 글 쓰기 권한이 있는지 여부
     */
    protected function checkWritePermission($boardConfig, $memberData)
    {
        // 글 쓰기 권한 체크 로직
        return (!empty($memberData) && $memberData['member_level'] >= $boardConfig['write_level']);
    }

    /**
     * 파일 다운로드 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 다운로드 권한이 있는지 여부
     */
    protected function checkDownloadPermission($boardConfig, $articleData, $memberData)
    {
        // 파일 다운로드 권한 체크 로직
        if ($this->isOwnArticle($articleData, $memberData)) {
            return true; // 자신의 글이면 다운로드 허용
        }

        return (!empty($memberData) && $memberData['member_level'] >= $boardConfig['download_level']);
    }

    /**
     * 댓글 쓰기 권한 체크 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 댓글 쓰기 권한이 있는지 여부
     */
    protected function checkCommentPermission($boardConfig, $articleData, $memberData)
    {
        // 댓글 쓰기 권한 체크 로직
        return (!empty($memberData) && $memberData['member_level'] >= $boardConfig['comment_level']);
    }

    /**
     * 사용자가 자신의 글인지 확인하는 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 사용자가 자신의 글인지 여부
     */
    protected function isOwnArticle($articleData, $memberData)
    {
        // 자신의 글인지 확인
        return !empty($memberData) && $articleData['mb_id'] === $memberData['mb_id'];
    }

    /**
     * 비회원의 읽기 권한을 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 비회원이 읽기 권한이 있는지 여부
     */
    protected function isGuestWithReadPermission($boardConfig, $articleData, $memberData)
    {
        // 비회원의 읽기 권한 체크
        return empty($memberData) && $articleData['mb_id'] === '' && 
               $boardConfig['read_level'] === 0 && 
               $articleData['user_ip'] === CommonHelper::getUserIp();
    }

    /**
     * 게시글이 공개된 상태인지 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @return bool 게시글이 공개된 상태인지 여부
     */
    protected function isPublicArticle($boardConfig, $articleData)
    {
        // 공개된 글인지 확인
        return $boardConfig['read_level'] === 0 && 
               $articleData['read_level'] === 0 && 
               $articleData['user_ip'] !== CommonHelper::getUserIp();
    }

    /**
     * 회원이 읽기 권한을 가지고 있는지 확인하는 메서드
     *
     * @param array $boardConfig 게시판 설정 배열
     * @param array $articleData 게시글 데이터 배열
     * @param array $memberData 회원 데이터 배열
     * @return bool 회원이 읽기 권한이 있는지 여부
     */
    protected function hasReadLevelPermission($boardConfig, $articleData, $memberData)
    {
        // 회원이 읽기 권한을 가지고 있는지 확인
        return !empty($memberData) && (
            ($articleData['real_level'] > 0 && $memberData['member_level'] >= $articleData['real_level']) ||
            ($boardConfig['real_level'] > 0 && $memberData['member_level'] >= $boardConfig['real_level'])
        );
    }

    /**
     * 조회수 업데이트 및 포인트 처리 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @param array|null $memberData 회원 데이터 배열 (옵션)
     * @param array|null $boardConfig 게시판 설정 배열 (옵션)
     * @return bool 조회수 업데이트 및 포인트 처리 성공 여부
     */
    protected function processView($articleData, $memberData = null, $boardConfig = null)
    {
        // 조회수 업데이트 및 포인트 처리 로직
        if ($memberData !== null && $boardConfig !== null) {
            if ($this->updateArticleMemberPoint($memberData, $boardConfig, $articleData) === false) {
                return false;
            }
        }
        $this->articleViewCountUpdate($articleData);
        return true;
    }

    /**
     * 게시글 조회수 업데이트 메서드
     *
     * @param array $articleData 게시글 데이터 배열
     * @return bool 조회수 업데이트 성공 여부
     */
    public function articleViewCountUpdate($articleData)
    {
        return $this->boardsModel->articleViewCountUpdate($articleData);
    }

    /**
     * 회원 포인트 업데이트 메서드 (예시)
     *
     * @param array $articleData 게시글 데이터 배열
     * @return bool 포인트 업데이트 성공 여부
     */
    public function updateArticleMemberPoint($articleData)
    {
        return true;
        // 실제 포인트 업데이트 로직은 아래 주석을 사용하여 구현
        // return $this->boardsModel->updateArticleMemberPoint($articleData);
    }
}