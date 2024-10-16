<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?php echo number_format($totalItems); ?></b> 건</span>
            </span>
            <?= $searchSelectBox['pagenum'] ?>
            <?= $searchSelectBox['ct_page_type'] ?>
        </div>
        <div class="local-auto">
        </div>
    </div>
    </form>
    <form name="flist" id="flist">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/settings/clauseListModify" data-callback="updateClauseModify">선택수정</a>
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/settings/clauseListDelete" data-callback="updateClauseDelete">선택삭제</a>
                <a href="/admin/settings/clauseForm" class="btn btn-fill-accent">이용약관 등록</a>
            </div>
        </div>
    </div>
    <div class="table-container">
        <div class="table-list-container">
            <ul class="table-list-wrapper">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 text-center">선택</div>
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-120 text-center">ID</div>
                        <div class="list-col col-custom-200 text-center">페이지 분류</div>
                        <div class="list-col col-custom-220 text-center">제목</div>
                        <div class="list-col col-custom-auto text-center">연결 URL</div>
                        <div class="list-col col-custom-80 text-center">구분</div>
                        <div class="list-col col-custom-80 text-center">사용</div>
                        <div class="list-col col-custom-60 text-center">순서</div>
                        <div class="list-col col-custom-100 text-center">등록일</div>
                        <div class="list-col col-custom-100 text-center">수정일</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($clauseList)) {
                    foreach($clauseList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['ct_id'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['ct_page_id'].'</div>';
                                echo '<div class="list-col col-custom-200 text-center">'.implode(",", $val['ct_page_type']).'</div>';
                                echo '<div class="list-col col-custom-220 text-center">'.$val['ct_subject'].'</div>';
                                echo '<div class="list-col col-custom-auto text-center">연결 URL</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['kindSelect'].'</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['useSelect'].'</div>';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="text" name="listData[ct_order]['.$key.']" value="'.$val['ct_order'].'" class="frm_input frm_full text-center" data-proto="'.$val['ct_order'].'">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.substr($val['ct_datetime'],0,10).'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.substr($val['ct_datetime'],0,10).'</div>';
                                echo '<div class="list-col col-custom-100 text-center">';
                                    echo '<a href="/admin/settings/clauseForm/'.$val['ct_id'].$queryString.'" class="btn btn-ssm btn-fill-accent">수정</a>';
                                    echo '<a href="javascript:void(0);" onclick="confirmDeleteBefore(this);" data-target="/admin/settings/clauseItemDelete" data-no="'.$val['ct_id'].'" data-callback="updateClauseDelete" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
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
    <?= $this->renderPagination($paginationData); ?>
</div>

<script>
App.registerCallback('updateClauseDelete', function(data) {
    document.location.reload();
});
</script>