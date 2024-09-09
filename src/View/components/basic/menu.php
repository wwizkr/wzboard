<!-- components/skinname/menu.php -->
<?php
function print_menu_data($config_domain, $menu = array())
{
    if (empty($menu)) {
        echo '<div class="menu_empty"><a href="/admin/settings/menu.php" target="_blank">메뉴가 생성되어 있지 않습니다. 메뉴를 생성하세요</a></div>';
        return false;
    }

    // device_type 값을 사용하여 모바일 여부를 결정
    $is_mobile = ($config_domain['device_type'] === 'mo');
    $swiper_wrapper = $is_mobile ? 'swiper-wrapper' : '';
    $swiper_slide = $is_mobile ? 'swiper-slide' : '';

    // 템플릿 파일 경로 설정
    $templatePath = __DIR__ . '/menuTemplate.html';
    $template = file_get_contents($templatePath);

    if ($template === false) {
        return '템플릿 파일을 찾을 수 없습니다.';
    }

    $output = '<ul class="me_1dul ' . $swiper_wrapper . '">';

    foreach ($menu as $v) {
        if (($v['me_pc_use'] != '1' && !$is_mobile) || ($v['me_mo_use'] != '1' && $is_mobile) || ($v['me_pa_use'] == '2' && $is_mobile)) {
            continue;
        }

        $me_link = $v['me_link'];
        $me_name = generate_menu_name($v);

        // 메뉴 아이템 템플릿에 데이터 바인딩
        $menuHtml = str_replace(
            ['{{depth}}', '{{code}}', '{{swiperSlide}}', '{{class}}', '{{link}}', '{{target}}', '{{name}}', '{{children}}'],
            [$v['me_depth'], $v['me_code'], $swiper_slide, $v['me_class'], $me_link, $v['me_target'], $me_name, print_sub_menu($v['children'], $v['me_depth'] + 1, $config_domain)],
            $template
        );

        $output .= $menuHtml;
    }

    $output .= '</ul>';
    echo $output;
}

function print_sub_menu($children, $depth, $config_domain)
{
    if (empty($children)) return '';

    // 템플릿 파일 경로 설정
    $templatePath = __DIR__ . '/menuTemplate.html';
    $template = file_get_contents($templatePath);

    $output = '<ul class="me_' . $depth . 'dul">';
    foreach ($children as $menu) {
        if ($menu['me_pc_use'] != '1') {
            continue;
        }

        $me_link = $menu['me_link'];
        $me_name = generate_menu_name($menu);

        // 서브 메뉴 아이템 템플릿에 데이터 바인딩
        $subMenuHtml = str_replace(
            ['{{depth}}', '{{code}}', '{{swiperSlide}}', '{{class}}', '{{link}}', '{{target}}', '{{name}}', '{{children}}'],
            [$menu['me_depth'], $menu['me_code'], '', $menu['me_class'], $me_link, $menu['me_target'], $me_name, print_sub_menu($menu['children'], $menu['me_depth'] + 1, $config_domain)],
            $template
        );

        $output .= $subMenuHtml;
    }
    $output .= '</ul>';
    return $output;
}

function generate_menu_name($menu)
{
    $span_style = '';
    if ($menu['me_fcolor']) {
        $span_style .= 'color:' . $menu['me_fcolor'] . ';';
    }
    if ($menu['me_fsize'] > 0) {
        $span_style .= 'font-size:' . $menu['me_fsize'] . 'px;';
    }
    if ($menu['me_fweight'] == 1) {
        $span_style .= 'font-weight:bold;';
    }
    $style_attribute = $span_style ? ' style="' . $span_style . '"' : '';

    return '<span class="me_' . $menu['me_depth'] . 'ds"' . $style_attribute . '>' . htmlspecialchars($menu['me_name']) . '</span>';
}

if (isset($config_domain) && isset($menuData)) {
    print_menu_data($config_domain, $menuData);
} else {
    echo "<p>메뉴 데이터를 찾을 수 없습니다.</p>";
}
?>
