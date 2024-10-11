<?php
// src/Admin/Helper/AdminCommonHelper.php

namespace Web\Admin\Helper;

use Web\PublicHtml\Core\DependencyContainer;

class AdminCommonHelper
{
    public static function makeSelectBox(string $name = '', array $options = [], string $value = null, string $id = null, string $class = null): string
    {
        $isId = $id ? 'id="'.$id.'"' : '';

        $str = '';
        $str .= '<select name="'.$name.'" '.$isId.' class="form-select '.$class.'" data-proto="'.$value.'">'.PHP_EOL;
        $str .= '<option value="">선택</option>'.PHP_EOL;
        
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

    public static function makeCheckBox(string $name = '', array $options = [], string $value = null, string $id = null, string $class = null): string
    {
        $str = '<div class="check-box">';
        foreach($options as $key=>$val) {
            $_checked = (string)$key === $value ? 'checked' : '';
            $isId = $id ? $id.'_'.$key : 'check_'.$key;
            $str .= '<input type="check" name="'.$name.'" id="'.$isId.'" value="'.$key.'" class="input-check '.$class.'" '.$_checked.'>';
            $str .= '<label for="'.$isId.'">'.$val.'</label>';
        }
        $str .= '</div>'.PHP_EOL;

        return $str;
    }
}