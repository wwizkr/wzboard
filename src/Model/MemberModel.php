<?php
// 파일위치 : src/Model/MemberModel.php

namespace Web\PublicHtml\Model;

use PDO;
use PDOException;
use Web\PublicHtml\Traits\DatabaseHelperTrait;
use Web\PublicHtml\Helper\DependencyContainer;

class MemberModel
{
    use DatabaseHelperTrait;

    private $db;
    private $config_domain;

    /**
     * 생성자: 의존성 주입을 통해 데이터베이스 연결을 설정합니다.
     */
    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
        $this->config_domain = $container->get('config_domain');
    }
    
    /*
     * 회원의 개별 정보를 가져옴
     * @param email [email 또는 mb_id]
     */
    public function getMemberData($email)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['mb_id'] = ['s', $email];
        $where['email'] = ['s', $email, 'or'];
        $options = [
            'order' => 'mb_no DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select',$this->getTableName('members'),$param,$where,$options);

        return $result[0];
    }

    /*
     * 회원의 개별 레벨 정보를 가져옴
     * @param level 
     */
    public function getMemberLevelData($level)
    {
        $param = [];
        $where = [];
        $where['cf_id'] = ['i', $this->config_domain['cf_id']];
        $where['level_id'] = ['i', $level];
        $options = [
            'order' => 'level_id DESC',
            'limit' => 1,
        ];
        $result = $this->db->sqlBindQuery('select',$this->getTableName('members_level'),$param,$where,$options);

        return $result[0];
    }
}
