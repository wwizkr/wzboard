<?php
// 파일 위치: src/Service/NaverShopService.php

namespace Web\PublicHtml\Service;

use Web\PublicHtml\Core\DependencyContainer;
use Web\PublicHtml\Helper\CommonHelper;
use Web\PublicHtml\Helper\CacheHelper;

class NaverShopService
{
    protected $container;
    protected $config_domain;

    public function __construct(DependencyContainer $container) {
        $this->container = $container;
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
    }

    public function searchRank($data) {
        $storeName = CommonHelper::validateParam('storeName', 'string', '', $data['storeName'], null);
        $itemCode = CommonHelper::validateParam('itemCode', 'string', '', $data['itemCode'], null);
        $matchCode = CommonHelper::validateParam('matchCode', 'string', '', $data['matchCode'], null);
        $searchKeyword = CommonHelper::validateParam('searchKeyword', 'string', '', $data['searchKeyword'], null);
        $oQuery  = CommonHelper::validateParam('oQuery', 'string', '', $data['oQuery'], null);
        $adQuery  = CommonHelper::validateParam('adQuery', 'string', '', $data['adQuery'], null);
        $clientId = CommonHelper::validateParam('clientId', 'string', '', $data['clientId'], null);
        $clientSecret = CommonHelper::validateParam('clientSecret', 'string', '', $data['clientSecret'], null);

        $searchData = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'storeName' => $storeName,
            'itemCode' => $itemCode,
            'matchCode' => $matchCode,
            'searchKeyword' => $searchKeyword ,
            'oQuery' => $oQuery,
            'adQuery' => $adQuery,
        ];

        //return $searchData;
        
        // 가격비교 원부 검색
        if ($matchCode) {
            $result = $this->searchNaverMatchItem($searchData);

            return $result;
        }
        
        // 목록검색
        $result = $this->searchNaverShopItem($searchData);
        return $result;
    }

    private function getNaverApiHeader($clientId, $clientSecret)
    {
        return [
            "X-Naver-Client-Id: ".$clientId,
            "X-Naver-Client-Secret: ".$clientSecret,
        ];
    }

    private function getNaverWebHeader()
    {
        return [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: max-age=0',
            'Sec-Ch-Ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
            'Sec-Ch-Ua-Arch: "x86"',
            'Sec-Ch-Ua-Bitness: "64"',
            'Sec-Ch-Ua-Full-Version-List: "Chromium";v="122.0.6261.131", "Not(A:Brand";v="24.0.0.0", "Google Chrome";v="122.0.6261.131"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Model: ""',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Ch-Ua-Platform-Version: "10.0.0"',
            'Sec-Ch-Ua-Wow64: ?0',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        ];
    }

    private function getNaverMatchPageApiHeader($matchCode)
    {
        return [
            'Accept: application/json, text/plain, */*',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
            'Priority: u=1, i',
            'Referer: https://search.shopping.naver.com/catalog/'.$matchCode,
            'Sec-Ch-Ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
            'Sec-Ch-Ua-Arch: "x86"',
            'Sec-Ch-Ua-Bitness: "64"',
            'Sec-Ch-Ua-Form-Factors: "Desktop"',
            'Sec-Ch-Ua-Full-Version-List: "Not/A)Brand";v="8.0.0.0", "Chromium";v="126.0.6478.127", "Google Chrome";v="126.0.6478.127"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Model: ""',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Ch-Ua-Platform-Version:"10.0.0"',
            'Sec-Ch-Ua-Wow64: ?0',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
        ];
    }
    
    // 가격비교 원부 검색
    private function searchNaverMatchItem(array $searchData)
    {
        $ranking = 0;

        $url = "https://search.shopping.naver.com/catalog/".$searchData['matchCode'];
        $headers = $this->getNaverWebHeader();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36');
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //error_log("Server:::".print_r($_SERVER['HTTP_USER_AGENT'], true));

        $pattern = '/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s';
        if($status_code == 200 && preg_match($pattern,$response,$matches)) {
            $jsonData = json_decode($matches[1],true);
            
            // 가격비교 페이지 카테고리탭 코드: 페이지 진입시 노출되는 탭코드
            $categorySequence = isset($jsonData['props']['pageProps']['initialState']['catalog']['products'][2]['param']['purchaseConditionSequence']) ?
                        $jsonData['props']['pageProps']['initialState']['catalog']['products'][2]['param']['purchaseConditionSequence'] :
                        '';

            // 가격비교 페이지 진입 시 매칭되어 있는 쇼핑몰 목록 1등~10등
            $matchItems = isset($jsonData['props']['pageProps']['initialState']['catalog']['products'][2]['productsPage']['products']) ?
                            $jsonData['props']['pageProps']['initialState']['catalog']['products'][2]['productsPage']['products'] :
                            [];
            
            if (!empty($matchItems)) { //$val['nvMid'] = $searchData['itemCode']
                foreach($matchItems as $key => $val) {
                    if ($searchData['storeName'] == $val['mallName']) {
                        $ranking = $key + 1;
                    }
                }
            }
            
            // 진입페이지 내에 매칭이 되어 있습니다.
            if ($ranking > 0) {
                return $ranking;
            }

            // 진입페이지에 매칭이 되지 않았다면 더보기 페이지로 이동합니다.
            $ranking = $this->searchNaverMatchProductPage($searchData, $categorySequence);
        }

        return $ranking;
    }

    // 가격비교 더보기 페이지 검색
    private function searchNaverMatchProductPage(array $searchData, string $categorySequence)
    {
        $headers = $this->getNaverMatchPageApiHeader($searchData['matchCode']);
        $pageSize = 20;
        $is_break = false;
        $ranking = 0;

        $params = [
            'arrivalGuarantee'=>'false',
            'cardPrice'=>'false',
            'deliveryToday'=>'false',
            'fastDelivery'=>'false',
            'isNPayPlus'=>'false',
            'nvMid'=>$searchData['matchCode'],
            'pageSize'=>$pageSize,
            'pr'=>'PC',
            'sort'=>'LOW_PRICE',
            'withFee'=>'true',
            'catalogType'=>'BRAND',
            'exposeAreaName'=>'SELLER_BY_PRICE',
            'inflow'=>'brc',
            'isManual'=>'true',
            'purchaseConditionSeq1'=>$categorySequence,
        ];

        for ($i = 0; $i <= 10; $i++) {
            $params['page'] = $i+1;

            $query = http_build_query($params);
            $url = 'https://search.shopping.naver.com/api/catalog/'.$searchData['matchCode'].'/products?'.$query;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($status_code == 200) {
                $result = json_decode($response,true);
                $mallData = $result['result']['products'];
                
                foreach($mallData as $key=>$val) {
                    if($searchData['storeName'] == $val['mallName']) {
                        $ranking = $i == 0 ? $key+1 : ($i*$pageSize) + ($key+1);;
                        $is_break = true;
                        break;
                    }
                }
            } else {
                $rangking = $status_code;
            }
        }

        return $ranking;
    }
    
    // 쇼핑 목록 검색
    private function searchNaverShopItem(array $searchData)
    {
        $headers = $this->getNaverApiHeader($searchData['clientId'], $searchData['clientSecret']);
        $display = 40;
        $keyword = $searchData['searchKeyword'];
        $storeName = $searchData['storeName'];

        $n = 0;
        $ranking = 0;
        $is_break = false;
        for($i = 0; $i < 25; $i++) {
            $start = ($i * $display) + 1;
            $pageIndex = $i + 1;

            $params = [
                'query' => $keyword,
                'display' => $display,
                'start' => $start,
                'filter'=> '',
                'exclude' => '',
            ];

            $query = http_build_query($params);
            $url = 'https://openapi.naver.com/v1/search/shop.json?'.$query;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $list = $response ? json_decode($response,true) : array();

            if (!empty($list['items'])) {
                foreach($list['items'] as $key=>$item) {
                    if ($item['productId'] == $searchData['itemCode']) {
                        $ranking = $start + $key;
                        $is_break = true;
                        break;
                    }
                }
            }

            if ($is_break) {
                break;
            }
        }
        
        return $ranking;
    }
}