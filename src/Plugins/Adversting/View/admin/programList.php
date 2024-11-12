<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?= $totalItems; ?></b> 건</span>
            </span>
        </div>
        <div class="local-auto">
            <div class="frm-input-row">
                <div class="frm-input wfpx-100">
                    <input type="hidden" name="filter[]" value="companyName">
                    <input type="text" name="search" id="search" value="<?php echo $_GET['search'] ?? '' ?>" class="frm_input frm_full">
                </div>
                <div class="frm-input frm-ml">
                    <input type="submit" class="btn btn-fill-accent" value="검색">
                </div>
            </div>
        </div>
    </div>
    </form>

    <form name="flist" id="flist">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <!--
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/adversting/admin/programListModify" data-callback="updateListModify">선택수정</a>
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/adversting/admin/programListDelete" data-callback="updateListDelete">선택삭제</a>
                -->
                <a href="/adversting/admin/programForm" class="btn btn-fill-accent">광고 프로그램 등록</a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-list-container">
            <ul class="table-list-wrapper">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <!--
                        <div class="list-col col-custom-60 text-center">
                            <input type="checkbox" name="listCheckAll" value="1" id="listCheckAll" class="list-check-all">
                            <label for="listCheckAll" class="sound-only">선택</label>
                        </div>
                        -->
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-160 text-center">프로그램명</div>
                        <div class="list-col col-custom-auto text-center">URL</div>
                        <div class="list-col col-custom-120 text-center">판매가</div>
                        <div class="list-col col-custom-100 text-center">운영일단위</div>
                        <div class="list-col col-custom-140 text-center">유입수</div>
                        <div class="list-col col-custom-70 text-center">타수</div>
                        <div class="list-col col-custom-70 text-center">UI</div>
                        <div class="list-col col-custom-140 text-center">접수마감</div>
                        <div class="list-col col-custom-140 text-center">구동시작</div>
                        <div class="list-col col-custom-90 text-center">운영여부</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($programList)) {
                    foreach($programList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                //echo '<div class="list-col col-custom-60 text-center">';
                                //    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['no'].'" id="check_'.$key.'" class="list-check">';
                                //    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                //echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-160 text-center">'.$val['companyName'].'</div>';
                                echo '<div class="list-col col-custom-auto text-center">'.$val['siteUrl'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">';
                                    echo '<div class="frm-input-row justify-center">';
                                        echo '<div class="frm-input wfpx-70">';
                                            echo '<input type="text" name="listData['.$key.'][supplyPrice]" value="'.$val['marketPrice'].'" data-proto="'.$val['marketPrice'].'" class="frm_input frm_full mask-num text-right">';
                                        echo '</div>';
                                        echo '<div class="frm-input input-append">';
                                            echo '<span class="frm_text">원</span>';
                                        echo '</div>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-100 text-center">';
                                    echo '<div class="frm-input-row justify-center">';
                                        echo '<div class="frm-input wfpx-50">';
                                            echo '<input type="text" name="listData['.$key.'][operateUnit]" value="'.$val['operateUnit'].'" data-proto="'.$val['operateUnit'].'" class="frm_input frm_full text-right">';
                                        echo '</div>';
                                        echo '<div class="frm-input input-append">';
                                            echo '<span class="frm_text">일</span>';
                                        echo '</div>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-140 text-center">';
                                    echo '<input type="text" name="listData['.$key.'][flowCount]" value="'.$val['flowCount'].'" data-proto="'.$val['flowCount'].'" class="frm_input frm_full">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-70 text-center">';
                                    echo '<select name="listData['.$key.'][clickCountCheck]" data-proto="'.$val['clickCountCheck'].'" class="frm_input">';
                                    foreach(['Y'=>'예','N'=>'아니요'] as $itemKey=>$itemVal) {
                                        $selected = $itemKey === $val['clickCountCheck'] ? 'selected' : '';
                                        echo '<option value="'.$itemKey.'">'.$itemVal.'</option>';
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-70 text-center">';
                                    echo '<select name="listData['.$key.'][existsUi]" data-proto="'.$val['existsUi'].'" class="frm_input">';
                                    foreach(['Y'=>'예','N'=>'아니요'] as $itemKey=>$itemVal) {
                                        $selected = $itemKey === $val['existsUi'] ? 'selected' : '';
                                        echo '<option value="'.$itemKey.'">'.$itemVal.'</option>';
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-140 text-center">';
                                    echo '<input type="text" name="listData['.$key.'][closeTime]" value="'.$val['closeTime'].'" data-proto="'.$val['closeTime'].'" class="frm_input frm_full">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-140 text-center">';
                                    echo '<input type="text" name="listData['.$key.'][startTime]" value="'.$val['startTime'].'" data-proto="'.$val['startTime'].'" class="frm_input frm_full">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-90 text-center">';
                                    echo '<select name="listData['.$key.'][status]" data-proto="'.$val['status'].'" class="frm_input">';
                                    foreach(['운영중','운영중지'] as $itemKey=>$itemVal) {
                                        $selected = $itemKey === $val['status'] ? 'selected' : '';
                                        echo '<option value="'.$itemKey.'">'.$itemVal.'</option>';
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="list-col list-col-row col-custom-100 text-center">';
                                    echo '<a href="/adversting/admin/programForm/'.$val['no'].$queryString.'" class="btn btn-ssm btn-fill-accent">수정</a>';
                                    echo '<a href="javascript:void(0);" onclick="confirmDeleteBefore(this);" data-target="/adversting/admin/programDelete" data-no="'.$val['no'].'" data-callback="updateItemDelete" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    </form>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>

<script>
App.registerCallback('updateListModify', function(data) {
    //location.reload();
});

App.registerCallback('updateListDelete', function(data) {
    //location.reload();
});

App.registerCallback('updateItemDelete', function(data) {
    //location.reload();
});
</script>