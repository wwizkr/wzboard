<?php
// src/View/MenuView.php

namespace Web\PublicHtml\View;

class MenuView
{
    public function renderMenu($menuData)
    {
        if (isset($menuData) && !empty($menuData)) {
            echo '<ul>';
            foreach ($menuData as $menuItem) {
                echo '<li>' . htmlspecialchars($menuItem['name']) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>메뉴가 없습니다.</p>';
        }
    }
}