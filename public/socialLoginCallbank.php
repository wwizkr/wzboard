<?php

require_once __DIR__ . '/../bootstrap.php';

use Web\Controller\SocialLoginController;

$controller = new SocialLoginController();
$controller->callback();