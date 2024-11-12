<?php
// /src/Plugins/Adversting/config.php
namespace Plugins\Adversting;

class Config
{
    public function getStoreType()
    {
        return [
            'navershop' => '네이버 쇼핑',
        ];
    }

    public function getStoreKind()
    {
        return [
            'smartstore' => '스마트 스토어',
            'mystore' => '자사몰',
        ];
    }

    public function getAdverstingLevel()
    {
        return [
            'agency' => 8,
            'agent' => 7,
            'seller' => 5,
        ];
    }

    public function getWzApiInfo()
    {
        return [
            'url' => 'https://wwiz.co.kr/api',
            'ver' => 'v1',
        ];
    }

    public function getNaverShopApiInfo()
    {
        return [
            'clientId' => 'p2gagzUeEDPEVjXvKO9e',
            'clientSecret' => 'uGE28oeieq',
        ];
    }
}