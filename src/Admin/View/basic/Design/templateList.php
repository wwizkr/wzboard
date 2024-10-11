<?php
use Web\Admin\Helper\AdminCommonHelper;
?>
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/template/templateForm">템플릿 등록</a>
        </div>
    </div>
</div>
<form name="flist" id="flist">
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <h2>목록</h2>
        <div class="p-3 table-list table-list table-list-scroll">
            <ul class="list-group">
                <li class="list-group-item list-group-head">
                    <div class="row list-group-row">
                        <div class="col-custom-60 list-group-col text-center">선택</div>
                        <div class="col-custom-140 list-group-col text-center">ID</div>
                        <div class="col-custom-160 list-group-col text-center">출력위치</div>
                        <div class="col-custom-80 list-group-col text-center">순서</div>
                        <div class="col-custom-80 list-group-col text-center">칸수</div>
                        <div class="col-custom-260 list-group-col text-center">관리용제목</div>
                        <div class="col list-group-col text-center">출력아이템</div>
                        <div class="col-custom-120 list-group-col text-center">사용</div>
                        <div class="col-custom-100 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if (!empty($listData)) {
                    foreach($listData as $key=>$val) {
                        $tmp = explode(",",$val['ct_list_box_itemtype']);
                        $box_item = array();
                        foreach($tmp as $k=>$v) {
                            $box_item[] = $templateItems[$v];
                        }
                        $box_items = implode(",",$box_item);
                        echo '<li class="list-group-item list-group-body" data-bunch="'.$key.'">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-custom-60 list-group-col text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['ct_id'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="d-none">선택</label>';
                                echo '</div>';
                                echo '<div class="col-custom-140 list-group-col text-center">'.$val['ct_section_id'].'</div>';
                                echo '<div class="col-custom-160 list-group-col text-center">'.$templatePosition[$val['ct_position']].'</div>';
                                echo '<div class="col-custom-80 list-group-col text-center">';
                                    echo '<input type="text" name="ct_order['.$key.']" value="'.$val['ct_order'].'" data-proto="'.$val['ct_order'].'" class="form-control">';
                                echo '</div>';
                                echo '<div class="col-custom-80 list-group-col text-center">'.$val['ct_list_box_cnt'].'</div>';
                                echo '<div class="col-custom-260 list-group-col text-center">'.$val['ct_admin_subject'].'</div>';
                                echo '<div class="col list-group-col text-center">'.$box_items.'</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">'.$val['useSelect'].'</div>';
                                echo '<div class="col-custom-100 list-group-col list-group-button text-center">';
                                    echo '<a href="/admin/template/templateForm?ct_id='.$val['ct_id'].'">수정</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
</form>