<?php
// src/Admin/Helper/AdminCommonHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;

class AdminCommonHelper
{
    public static function pagingOption() {
        return [
            '15' => '15건 출력',
            '30' => '30건 출력',
            '50' => '50건 출력',
            '100' => '100건 출력',
            '300' => '300건 출력',
        ];
    }

    public static function makeSelectBox(string $name = '', array $options = [], string $value = null, ?string $id = null, ?string $class = null, ?string $title = null): string
    {
        $isId = $id ? 'id="'.$id.'"' : '';

        if (empty($options)) {
            $options = self::pagingOption();
        }

        $str = '';
        $str .= '<select name="'.$name.'" '.$isId.' class="'.$class.'" data-proto="'.$value.'">'.PHP_EOL;
        if ($title !== null) {
            $str .= '<option value="">'.$title.'</option>'.PHP_EOL;
        } else {
            $str .= '<option value="">선택</option>'.PHP_EOL;
        }
        
        foreach($options as $key=>$val) {
            $_selected = (string)$key === $value ? 'selected' : '';
            $str .= '<option value="'.$key.'" '.$_selected.'>'.$val.'</option>';
        }
        $str .= '</select>'.PHP_EOL;

        return $str;
    }

    public static function makeRadioBox(string $name = '', array $options = [], string $value = null, string $id = null, string $class = null): string
    {
        $str = '<div class="radio-box">';
        foreach($options as $key=>$val) {
            $_checked = (string)$key === $value ? 'checked' : '';
            $isId = $id ? $id.'_'.$key : 'radio_'.$key;
            $str .= '<input type="radio" name="'.$name.'" id="'.$isId.'" value="'.$key.'" class="input-radio '.$class.'" '.$_checked.'>';
            $str .= '<label for="'.$isId.'">'.$val.'</label>';
        }
        $str .= '</div>'.PHP_EOL;

        return $str;
    }

    public static function makeCheckBox(string $name = '', array $options = [], array $value = [], ?string $id = null, ?string $class = null): string
    {
        $str = '';
        foreach($options as $key => $val) {
            $_checked = (!empty($value) && in_array((string)$key, array_map('strval', $value))) ? 'checked' : '';
            $isId = $id ? $id.'_'.$key : 'check_'.$key;
            $str .= '<div class="frm-check check-box">';
            $str .= '<input type="checkbox" name="'.$name.'[]" id="'.$isId.'" value="'.$key.'" class="input-check '.($class ?? '').'" '.$_checked.'>';
            $str .= '<label for="'.$isId.'">'.$val.'</label>';
            $str .= '</div>'.PHP_EOL;
        }
        
        return $str;
    }
}