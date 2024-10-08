<?php
namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;


class TemplateViewHelper
{
    protected $container;
    protected $table;
    protected $configProvider;
    protected $config_domain;

    public function __construct(DependencyContainer $container, $table = 'template')
    {
        $this->container = $container;
        $this->table = $table;
        $this->configProvider = $this->container->get('ConfigProvider');
        $this->config_domain = $this->container->get('ConfigHelper')->getConfig('config_domain');
    }

    public function render($templateData)
    {
        ob_start();
        foreach ($templateData as $template) {
            $this->renderTemplate($template);
        }
        return ob_get_clean();
    }

    protected function renderTemplate($template)
    {
        ?>
        <section id="<?php echo $template['ct_section_id']; ?>" class="section">
            <style>
                /* 섹션 스타일 */
                #<?php echo $template['ct_section_id']; ?> {
                    width: 100%;
                    <?php echo $template['padding']; ?>
                    <?php echo $template['bgcolor']; ?>
                    <?php if ($template['bgimage']): ?>
                        background: url('<?php echo $template['bgimage']; ?>') no-repeat center;
                        background-size: cover;
                    <?php endif; ?>
                }
                #<?php echo $template['ct_section_id']; ?> .templateSectInner {
                    position: relative;
                    width: 100%;
                    display: flex;
                    flex-wrap: wrap;
                }
                /* 박스 스타일 */
                <?php foreach ($template['boxes'] as $index => $box): ?>
                #tempbox_<?php echo $template['ct_id']; ?>_<?php echo $index; ?> {
                    flex: 1 1 <?php echo $box['boxWidth']; ?>;
                    max-width: <?php echo $box['boxWidth']; ?>;
                }
                @media (max-width: 768px) {
                    #tempbox_<?php echo $template['ct_id']; ?>_<?php echo $index; ?> {
                        flex: 1 1 100%;
                        max-width: 100%;
                    }
                }
                <?php endforeach; ?>
            </style>
            <div class="templateSect <?php echo $template['sectionWidth']; ?>">
                <div class="templateSectInner">
                    <?php foreach ($template['boxes'] as $index => $box): ?>
                        <?php $this->renderBox($template, $box, $index); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }

    protected function renderBox($template, $box, $index)
    {
        ?>
        <div id="tempbox_<?php echo $template['ct_id']; ?>_<?php echo $index; ?>" class="temp_box" <?php echo $box['display']; ?> <?php echo $box['effect']; ?>>
            <?php $this->renderSubject($template, $box, $index); ?>
            <div class="temp_box_inner" style="<?php echo $template['sectionHeight'] ? $template['sectionHeight'].'px;' : ''; ?>">
                <?php echo $this->renderBoxContent($box); ?>
            </div>
        </div>
        <?php
    }

    protected function renderSubject($template, $box, $index)
    {
        if ($box['subject']['view'] != 1) return;

        if ($box['subject']['image']) {
            echo $this->renderSubjectImage($template, $box, $index);
        } elseif ($box['subject']['text']) {
            echo $this->renderSubjectText($template, $box, $index);
        }
    }

    protected function renderSubjectImage($template, $box, $index)
    {
        ?>
        <div class="item-title-image item-title-image_<?php echo $template['ct_id']; ?>_<?php echo $index; ?> item-title__<?php echo $box['subject']['position']; ?>">
            <img src="<?php echo $box['subject']['image']; ?>">
            <?php if ($box['subject']['more_link'] && $box['subject']['more_url']): ?>
                <div class="title-more"><a href="<?php echo $box['subject']['more_url']; ?>">더보기</a></div>
            <?php endif; ?>
        </div>
        <?php
    }

    protected function renderSubjectText($template, $box, $index)
    {
        ?>
        <div class="item-title item-title_<?php echo $template['ct_id']; ?>_<?php echo $index; ?> item-title__<?php echo $box['subject']['position']; ?>">
            <?php
            if (!$box['copytext']['text']) {
                echo '<span class="item-title_subject subject__' . $index . '">' . $box['subject']['text'] . '</span>';
            } else {
                $this->renderSubjectWithCopytext($box, $index);
            }
            if ($box['subject']['more_link'] && $box['subject']['more_url']) {
                $this->renderMoreLink($box['subject']['more_url']);
            }
            ?>
        </div>
        <?php
    }

    protected function renderSubjectWithCopytext($box, $index)
    {
        if ($box['copytext']['position'] == 'left' || $box['copytext']['position'] == 'top') {
            echo '<span class="item-title_copytext copytext__'.$index.' copytext__'.$box['copytext']['position'].'">';
            echo $box['copytext']['text'];
            echo '<span class="item-title_subject subject__'.$index.'">'.$box['subject']['text'].'</span>';
            echo '</span>';
        } elseif ($box['copytext']['position'] == 'right' || $box['copytext']['position'] == 'bottom') {
            echo '<span class="item-title_subject subject__'.$index.'">';
            echo $box['subject']['text'];
            echo '<span class="item-title_copytext copytext__'.$index.' copytext__'.$box['copytext']['position'].'">'.$box['copytext']['text'].'</span>';
            echo '</span>';
        }
    }

    protected function renderMoreLink($url)
    {
        ?>
        <div class="title-more">
            <a href="<?php echo $url; ?>">더보기
                <span>
                    <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 12L7 6.5L1 1" stroke="#6B6B6B" stroke-linecap="square"></path>
                    </svg>
                </span>
            </a>
        </div>
        <?php
    }

    protected function renderBoxContent($box)
    {
        if ($box['item_dir'] == 'file') {
            $this->renderFile($box);
        } else {
            return $this->renderSkin($box);
        }
    }

    protected function renderFile($box)
    {
        $skin_file = WZ_SRC_PATH.'/View/Templates/'.$box['item_dir'].'/'.$box['skin_dir'];
        if (file_exists($skin_file)) {
            $container = $this->container;
            include $skin_file;
        } else {
            return $this->renderError();
        }
    }

    protected function renderSkin($box)
    {
        $template_file = WZ_SRC_PATH.'/View/Templates/'.$box['item_dir'].'/'.$box['skin_dir'].'/template.'.$box['item_dir'].'.html';
    
        if (file_exists($template_file)) {
            if (!empty($box['items'])) {
                $preparedData = $this->prepareDataForSkin($box);
                $template = file_get_contents($template_file);
                $output = $this->fillTemplateWithData($template, $preparedData);
                return $output;
            } else {
                return '<div class="box_empty"></div>';
            }
        } else {
            return $this->renderError();
        }
    }

    protected function fillTemplateWithData($template, $data)
    {
        // 아이템 템플릿 추출
        if (preg_match('/<!--ITEM_TEMPLATE_START-->(.*?)<!--ITEM_TEMPLATE_END-->/s', $template, $matches)) {
            $itemTemplate = $matches[1];
            // 전체 템플릿에서 아이템 템플릿 부분 제거
            $template = preg_replace('/<!--ITEM_TEMPLATE_START-->.*?<!--ITEM_TEMPLATE_END-->/s', '', $template);
        } else {
            $itemTemplate = '';
        }

        // 아이템 HTML 생성 (items가 있을 경우에만)
        $itemsHtml = '';
        if (isset($data['items']) && !empty($data['items']) && !empty($itemTemplate)) {
            foreach ($data['items'] as $item) {
                $itemHtml = $itemTemplate;
                foreach ($item as $key => $value) {
                    // 특수한 경우 처리 (예: 썸네일)
                    if ($key === 'thumb' && !empty($value)) {
                        $value = '<div class="list-col list-thumb">' . $value . '</div>';
                    }
                    $itemHtml = str_replace('{{'.$key.'}}', $value, $itemHtml);
                }
                // 사용되지 않은 플레이스홀더 제거
                $itemHtml = preg_replace('/{{[^}]+}}/', '', $itemHtml);
                $itemsHtml .= $itemHtml;
            }
        }

        // 전체 템플릿에 아이템 HTML 삽입
        $output = str_replace('{{items}}', $itemsHtml, $template);

        // replace 데이터 처리 (모든 단일 치환을 여기서 처리)
        if (isset($data['replace']) && is_array($data['replace'])) {
            foreach ($data['replace'] as $key => $value) {
                // null 체크 추가
                if ($value !== null) {
                    // 문자열로 강제 변환
                    $value = (string)$value;
                    $output = str_replace('{{'.$key.'}}', $value, $output);
                } else {
                    // null인 경우 빈 문자열로 대체하거나 다른 처리를 수행
                    $output = str_replace('{{'.$key.'}}', '', $output);
                }
            }
        }

        // 남아있는 플레이스홀더 제거
        $output = preg_replace('/{{[^}]+}}/', '', $output);

        return $output;
    }

    protected function prepareDataForSkin($box)
    {
        $method = 'prepareDataFor' . ucfirst($box['item_dir']);
        if (method_exists($this, $method)) {
            $box['boxId'] = $box['item_dir'] ? $box['item_dir'].'-box-'.$box['id'] : uniqid($box['item_dir'].'-');
            $prepareData = $this->$method($box);
            $prepareData['replace']['boxId'] = $box['boxId'];
            return $prepareData;
        }

        return [
            'replace' => $box['replace'] ?? [],
            'items' => $box['items'] ?? [],
        ];
    }
    
    // 게시판 최신글 출력 로직
    protected function prepareDataForBoard($box)
    {
        // 게시판 데이터 준비 로직
        $limit = $box['itemcnt'] ?? 5;
        $listStyle = $box['style']; // list or swiper
        $board = $box['items'][0]; // 게시판은 한개의 게시판 정보만 가져옴.
        
        $adminBoardsService = $this->container->get('AdminBoardsService');
        $boardsService = $this->container->get('BoardsService');
        $boardConfig = $adminBoardsService->getBoardsConfig($board['ci_pc_item']);
        
        if (empty($boardConfig)) {
            return [
                'result' => 'failure',
                'message' => '게시판 정보를 찾을 수 없습니다.',
                'items' => [],
            ];
        }
        
        $listData = $boardsService->getLatestArticleList($boardConfig, $limit);
        
        $processedArticles = [];
        foreach ($listData as $index => $article) {
            $processedArticles[] = [
                'num' => $limit - $index,
                'href' => '/board/' . $boardConfig['board_id'] . '/view/' . $article['no'] . '/' . $article['slug'],
                'articleNo' => $article['no'],
                'boardId' => $boardConfig['board_id'],
                'thumb' => $article['thumb'] ?? '',
                'title' => $article['title'],
                'slug' => $article['slug'],
                'nickName' => $article['nickName'],
                'hit' => number_format($article['view_count']),
                'comment' => $article['comment_count'] ?? 0,
                'date' => $article['date1'],
            ];
        }

        $replace = [
        ];
        
        return [
            'result' => 'success',
            'message' => '게시판 최신글 목록을 가져왔습니다.',
            'items' => $processedArticles,
            'replace' => $replace,
        ];
    }

    // 게시판 그룹 최신글 출력 로직
    protected function prepareDataForBoardgroup($box)
    {
        // 게시판 데이터 준비 로직
        $limit = $box['itemcnt'] ?? 5;
        $listStyle = $box['style']; // list or swiper
        $boardItems = $box['items'][0]['ci_pc_item'] ? explode(",", $box['items'][0]['ci_pc_item']) : [];
        
        $adminBoardsService = $this->container->get('AdminBoardsService');
        $boardsService = $this->container->get('BoardsService');
        
        $boardData = [];
        foreach($boardItems as $index => $board) {
            $boardData[$index] = $adminBoardsService->getBoardsConfig($board);
            $listData[$index] = $boardsService->getLatestArticleList($boardData[$index], $limit);
        }

        $boardTab = [];
        foreach($boardData as $index => $tab) {
            $boardTab[$index] = [
                'index' => $index,
                'boardName' => $tab['board_name'],
            ];
        }

        $jsonData = [];
        foreach($listData as $index => $articleData) {
            if (empty($articleData)) {
                continue;
            }

            foreach($articleData as $article) {
                $jsonData[$index][] = [
                    'num' => $limit - $index,
                    'href' => '/board/' . $boardData[$index]['board_id'] . '/view/' . $article['no'] . '/' . $article['slug'],
                    'articleNo' => $article['no'],
                    'boardId' => $boardData[$index]['board_id'],
                    'thumb' => $article['thumb'] ?? '',
                    'title' => $article['title'],
                    'slug' => $article['slug'],
                    'nickName' => $article['nickName'],
                    'hit' => number_format($article['view_count']),
                    'comment' => $article['comment_count'] ?? 0,
                    'date' => $article['date1'],
                ];
            }
        }
        
        $replace = [
            'jsonData' => !empty($jsonData) ? json_encode($jsonData) : '[]',
            'isSwiper' => $box['style'] === 'slide' ? 'true' : 'false',
        ];

        return [
            'result' => 'success',
            'message' => '게시판 최신글 목록을 가져왔습니다.',
            'items' => $boardTab,
            'replace' => $replace,
        ];
    }

    // 에디터 출력
    protected function prepareDataForEditor($box)
    {
        $replace = [
            'content' => $box['items'][0]['ci_content'],
        ];

        return [
            'result' => 'success',
            'message' => '에디터 내용을 가져왔습니다.',
            'items' => [],
            'replace' => $replace,
        ];
    }

    protected function prepareDataForImage($box)
    {
        $baseImage100 = $this->configProvider->get('image')['noImg100'];
        $baseImage430 = $this->configProvider->get('image')['noImg430'];
        $imagePath = WZ_STORAGE_PATH . '/template/' . $this->config_domain['cf_id'] . '/' . $this->table;
        
        // 이미지 목록 처리
        $items = [];
        foreach ($box['items'] ?? [] as $key => $val) {
            $pc_image = $val['ci_pc_item'] ?? '';
            $mo_image = $val['ci_mo_item'] ?? '';
            $link = $val['ci_link'] ?? '';
            $win = ($val['ci_new_win'] ?? 0) === 1 ? 'target="_blank"' : '';

            $pc_image_url = $pc_image && file_exists($imagePath . '/' . $pc_image)
                ? '/storage/template/' . $this->config_domain['cf_id'] . '/' . $this->table . '/' . $pc_image
                : $baseImage100;

            $mo_image_url = $mo_image && file_exists($imagePath . '/' . $mo_image)
                ? '/storage/template/' . $this->config_domain['cf_id'] . '/' . $this->table . '/' . $mo_image
                : ($pc_image_url !== $baseImage100 ? $pc_image_url : $baseImage430);

            $pc_string = $link
                ? "<a href=\"{$link}\" {$win}><img src=\"{$pc_image_url}\" style=\"width:100%;height:auto\"></a>"
                : "<img src=\"{$pc_image_url}\" style=\"width:100%;height:auto\">";

            $mo_string = $link
                ? "<a href=\"{$link}\" {$win}><img src=\"{$mo_image_url}\" style=\"width:100%;height:auto\"></a>"
                : "<img src=\"{$mo_image_url}\" style=\"width:100%;height:auto\">";

            $items[$key] = [
                'pcImage' => $pc_string,
                'moImage' => $mo_string,
                'swiperSlide' => $box['style'] === 'slide' ? 'swiper-slide' : '',
            ];
        }

        // Swiper 설정
        $swiperId = $box['boxId'];
        $swiperOptions = [
            'style' => $box['style'] ?? '',
            'slidesPerView' => $box['cols'] ?? 'auto',
            'navigation' => true,
            'pagination' => true,
            'touchRatio' => 1,
            'observer' => true,
            'observeParents' => true,
            // 필요에 따라 다른 옵션들을 추가할 수 있습니다.
        ];

        $swiperConfig = CommonHelper::getSwiperConfig($swiperId, $swiperOptions);

        $replace = [
            'swiperContainer' => $box['style'] === 'slide' ? 'swiper-container' : '',
            'swiperWrapper' => $box['style'] === 'slide' ? 'swiper-wrapper' : '',
            'swiperScript' => $swiperConfig['script'],
            'swiperHtml' => $swiperConfig['html'],
        ];

        return [
            'result' => 'success',
            'message' => '이미지 내용을 가져왔습니다.',
            'items' => $items,
            'replace' => $replace,
        ];
    }

    protected function renderError()
    {
        include(WZ_SRC_PATH.'/Core/ErrorRenderer.php');
    }
}