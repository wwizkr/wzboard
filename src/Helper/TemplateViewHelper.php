<?php
namespace Web\PublicHtml\Helper;

use Web\PublicHtml\Core\DependencyContainer;


class TemplateViewHelper
{
    protected $container;

    public function __construct(DependencyContainer $container)
    {
        $this->container = $container;
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
                <?php $this->renderBoxContent($box); ?>
            </div>
        </div>
        <?php
    }

    protected function renderSubject($template, $box, $index)
    {
        if ($box['subject']['view'] != 1) return;

        if ($box['subject']['image']) {
            $this->renderSubjectImage($template, $box, $index);
        } elseif ($box['subject']['text']) {
            $this->renderSubjectText($template, $box, $index);
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
            $this->renderSkin($box);
        }
    }

    protected function renderFile($box)
    {
        $skin_file = WZ_SRC_PATH.'/View/Templates/'.$box['item_dir'].'/'.$box['skin_dir'];
        if (file_exists($skin_file)) {
            include $skin_file;
        } else {
            $this->renderError();
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
                echo $output;
            } else {
                echo '<div class="box_empty"></div>';
            }
        } else {
            $this->renderError();
        }
    }

    private function getCacheKey($box)
    {
        // 고유한 캐시 키 생성
        return 'template_' . $box['item_dir'] . '_' . $box['skin_dir'] . '_' . md5(serialize($box));
    }

    // 캐시 무효화 메서드
    public function invalidateTemplateCache($itemDir, $skinDir)
    {
        $cacheKey = 'template_' . $itemDir . '_' . $skinDir . '_*';
        CacheHelper::clearCache($cacheKey);
    }

    protected function fillTemplateWithData($template, $data)
    {
        // 아이템 템플릿 추출
        if (!preg_match('/<!--ITEM_TEMPLATE_START-->(.*?)<!--ITEM_TEMPLATE_END-->/s', $template, $matches)) {
            throw new \Exception('Error: Item template not found');
        }

        $itemTemplate = $matches[1];
        
        // 전체 템플릿에서 아이템 템플릿 부분 제거
        $template = preg_replace('/<!--ITEM_TEMPLATE_START-->.*?<!--ITEM_TEMPLATE_END-->/s', '', $template);

        // 아이템 HTML 생성
        $itemsHtml = '';
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

        // 전체 템플릿에 아이템 HTML 삽입 및 나머지 데이터 치환
        $output = str_replace('{{items}}', $itemsHtml, $template);
        
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $output = str_replace('{{'.$key.'}}', $value, $output);
            }
        }

        // 남아있는 플레이스홀더 제거
        $output = preg_replace('/{{[^}]+}}/', '', $output);

        return $output;
    }

    protected function renderError()
    {
        include(WZ_SRC_PATH.'/View/ErrorRenderer.php');
    }

    protected function prepareDataForSkin($box)
    {
        $method = 'prepareDataFor' . ucfirst($box['item_dir']);
        if (method_exists($this, $method)) {
            return $this->$method($box);
        }
        return ['items' => $box['items']];
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
        
        return [
            'result' => 'success',
            'message' => '게시판 최신글 목록을 가져왔습니다.',
            'boxId' => $box['id'] ?? uniqid('board-'),
            'items' => $processedArticles,
        ];
    }
}