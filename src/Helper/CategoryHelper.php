<?php
// src/Helper/CategoryHelper.php

namespace Web\PublicHtml\Helper;

class CategoryHelper
{
    /**
     * 카테고리 데이터를 트리 구조로 생성하는 함수
     *
     * @param array $categoryData 카테고리 데이터 배열
     * @param string $idKey 각 카테고리 항목의 고유 식별자 키
     * @param string $parentKey 부모 카테고리 식별자 키
     * @return array 트리 구조로 정리된 카테고리 데이터
     */
    public static function generateCategoryTree(array $categoryData, $idKey = 'no', $parentKey = 'category_parent')
    {
        $tree = [];
        $indexed = [];

        // 1. 각 카테고리 항목을 인덱스로 정리하여 참조 저장
        foreach ($categoryData as &$categoryItem) {
            $categoryItem['children'] = []; // 자식 카테고리를 담을 배열 추가
            $indexed[$categoryItem[$idKey]] = &$categoryItem;
        }

        // 2. 부모-자식 관계를 설정하여 트리 구조 생성
        foreach ($indexed as &$categoryItem) {
            if ($categoryItem[$parentKey] != 0) {
                // 부모가 있는 경우, 해당 부모의 'children' 배열에 추가
                if (isset($indexed[$categoryItem[$parentKey]])) {
                    $indexed[$categoryItem[$parentKey]]['children'][] = &$categoryItem;
                }
            } else {
                // 최상위 카테고리(부모가 없는 경우)는 트리의 루트에 추가
                $tree[] = &$categoryItem;
            }
        }

        return $tree;
    }
}