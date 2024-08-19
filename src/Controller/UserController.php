<?php
// 파일 위치: /home/web/public_html/src/Controller/UserController.php

namespace Web\PublicHtml\Controller;

use Web\PublicHtml\Helper\DependencyContainer;
use Exception;

class UserController
{
    private $db;

    public function __construct(DependencyContainer $container)
    {
        $this->db = $container->get('db');
    }

    public function getUsers($vars = [])
    {
        try {
            $users = $this->db->sqlBindQuery('select', 'users', [], ['status' => ['i', 1]], [
                'field' => 'id, name, email',
                'order' => 'name ASC',
                'limit' => '10'
            ]);
            echo json_encode(['success' => true, 'data' => $users]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function createUser($vars = [])
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                throw new Exception('필수 필드가 누락되었습니다.');
            }

            $result = $this->db->sqlBindQuery('insert', 'users', [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'status' => 1
            ]);

            echo json_encode(['success' => true, 'message' => '사용자가 생성되었습니다.', 'id' => $result['insertId']]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateUser($vars = [])
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $vars['id'] ?? null;

            if (!$userId) {
                throw new Exception('사용자 ID가 필요합니다.');
            }

            $updateData = [];
            if (isset($data['name'])) $updateData['name'] = $data['name'];
            if (isset($data['email'])) $updateData['email'] = $data['email'];
            if (isset($data['password'])) $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $result = $this->db->sqlBindQuery('update', 'users', $updateData, ['id' => ['i', $userId]]);

            echo json_encode(['success' => true, 'message' => '사용자 정보가 업데이트되었습니다.', 'affectedRows' => $result['affectedRows']]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteUser($vars = [])
    {
        try {
            $userId = $vars['id'] ?? null;

            if (!$userId) {
                throw new Exception('사용자 ID가 필요합니다.');
            }

            $result = $this->db->sqlBindQuery('delete', 'users', [], ['id' => ['i', $userId]]);

            echo json_encode(['success' => true, 'message' => '사용자가 삭제되었습니다.', 'affectedRows' => $result['affectedRows']]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}