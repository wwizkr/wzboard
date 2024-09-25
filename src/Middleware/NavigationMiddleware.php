<?php
// 파일 위치: src/Middleware/NavigattionMiddleware.php
namespace Web\PublicHtml\Middleware;
use Web\PublicHtml\Core\DependencyContainer;

class NavigationMiddleware
{
    private $container;
    private $menuData;
    private $currentUri;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
        $this->menuData = $container->get('menu_datas');
        $this->setCurrentUri();
    }

    private function setCurrentUri()
    {
        // URI (경로 + 쿼리 스트링)
        $this->currentUri = $_SERVER['REQUEST_URI'];
        // URL 디코딩
        $this->currentUri = urldecode($this->currentUri);
    }

    public function buildNavigation()
    {
        // 회원 관련 페이지 처리
        $memberPages = ['/member'];
        if (in_array(strtolower($_SERVER['SCRIPT_FILENAME']), $memberPages)) {
            return ['me_code' => 'mb', 'navigator' => [['name' => '회원', 'link' => '/member']]];
        }

        $result = $this->findMatchingMenuWithQuery();
        
        if ($result) {
            return [
                'me_code' => $result['me_code'],
                'navigator' => $this->buildNavigator($result['path'])
            ];
        }

        return null;
    }

    private function findMatchingMenuWithQuery()
    {
        if (empty($this->currentUri)) {
            // 현재 URI가 비어있거나 null인 경우 처리
            return null;
        }

        $parsedUrl = parse_url($this->currentUri);
        
        if ($parsedUrl === false) {
            // parse_url이 실패한 경우 처리
            return null;
        }

        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? '';

        if ($query) {
            $queryParts = explode('&', $query);
            $partialUri = $path . '?';

            for ($i = count($queryParts); $i >= 0; $i--) {
                $currentQuery = implode('&', array_slice($queryParts, 0, $i));
                $currentUri = $i > 0 ? $partialUri . $currentQuery : $path;
                $result = $this->findMatchingMenu($this->menuData, $currentUri);
                if ($result) {
                    return $result;
                }
            }
        } else {
            return $this->findMatchingMenu($this->menuData, $path);
        }

        return null;
    }

    private function findMatchingMenu($menuItems, $currentUri, $path = [])
    {
        foreach ($menuItems as $item) {
            $newPath = array_merge($path, [$item]);
            
            if ($this->compareUrls($item['me_link'], $currentUri)) {
                return ['me_code' => $item['me_code'], 'path' => $newPath];
            }

            if (!empty($item['children'])) {
                $result = $this->findMatchingMenu($item['children'], $currentUri, $newPath);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    private function compareUrls($menuLink, $currentUri)
    {
        // 정확히 일치하는 경우
        if ($menuLink === $currentUri) {
            return true;
        }

        // 쿼리 스트링이 있는 경우 처리
        $menuParts = $menuLink ? parse_url($menuLink) : ['path' => '', 'query' => ''];
        $currentParts = $currentUri ? parse_url($currentUri) : ['path' => '', 'query' => ''];

        if ($menuParts['path'] === $currentParts['path']) {
            // 경로가 일치하면 쿼리 파라미터 확인
            if (isset($menuParts['query']) && isset($currentParts['query'])) {
                parse_str($menuParts['query'], $menuQuery);
                parse_str($currentParts['query'], $currentQuery);
                
                // 메뉴 링크의 모든 쿼리 파라미터가 현재 URI에 포함되어 있는지 확인
                foreach ($menuQuery as $key => $value) {
                    if (!isset($currentQuery[$key]) || $currentQuery[$key] !== $value) {
                        return false;
                    }
                }
                return true;
            } elseif (!isset($menuParts['query']) && !isset($currentParts['query'])) {
                return true;
            }
        }

        return false;
    }

    private function buildNavigator($path)
    {
        $navigator = [];
        foreach ($path as $item) {
            $navigator[] = [
                'me_code' => $item['me_code'],
                'name' => $item['me_name'],
                'link' => $item['me_link']
            ];
        }
        return $navigator;
    }

    public function getNavigationInfo()
    {
        return $this->buildNavigation();
    }
}