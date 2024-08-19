<?php
namespace Web\PublicHtml\Api\V1;

class UserController
{
    public function getUsers()
    {
        echo json_encode(['message' => 'Get Users API']);
    }

    public function createUser()
    {
        echo json_encode(['message' => 'Create User API']);
    }
}