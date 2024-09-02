<?php
//파일위치 src/Service/MembersService.php

namespace Web\PublicHtml\Service;

use  Web\PublicHtml\Model\MembersModel;

class MembersService
{
    protected $membersModel;

    public function __construct(MembersModel $membersModel)
    {
        $this->membersModel = $membersModel;
    }

    public function getMemberDataById($email=null)
    {
        return $this->membersModel->getMemberDataById($email);
    }
    
    public function getMemberLevelData($level=null)
    {
        return $this->membersModel->getMemberLevelData($level);
    }

    public function getMemberListData($currentPage, $page_rows, $searchQuery, $filters, $sort)
    {
        return $this->membersModel->getMemberListData($currentPage, $page_rows, $searchQuery, $filters, $sort);
    }

    public function getTotalMemberCount($searchQuery, $filters)
    {
        return $this->membersModel->getTotalMemberCount($searchQuery, $filters);
    }
}