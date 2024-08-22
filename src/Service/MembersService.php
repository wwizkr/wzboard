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

    public function getMemberData($email=null)
    {
        return $this->membersModel->getMemberData($email);
    }
    
    public function getMemberLevelData($level=null)
    {
        return $this->membersModel->getMemberLevelData($level);
    }
}