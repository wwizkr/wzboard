<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?php echo number_format($totalItems); ?></b> 건</span>
            </span>
        </div>
        <div class="local-auto">
            <div class="frm-input-row">
                <div class="frm-input wfpx-100">
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
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/boardadmin/boardListModify" data-callback="updateBoardListModify">선택수정</a>
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/boardadmin/boardListDelete" data-callback="updateBoardListDelete">선택삭제</a>
                <a href="/admin/boardadmin/boardform" class="btn btn-fill-accent">게시판 생성</a>
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
                        <div class="list-col col-custom-140 text-center">그룹명</div>
                        <div class="list-col col-custom-120 text-center">게시판 스킨</div>
                        <div class="list-col col-custom-120 text-center">게시판 ID</div>
                        <div class="list-col col-custom-auto text-center">게시판명</div>
                        <div class="list-col col-custom-60 text-center">읽기P</div>
                        <div class="list-col col-custom-60 text-center">쓰기P</div>
                        <div class="list-col col-custom-60 text-center">댓글P</div>
                        <div class="list-col col-custom-60 text-center">다운P</div>
                        <div class="list-col col-custom-200 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($boardList)) {
                    foreach($boardList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['no'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-140 text-center">'.$val['groupSelect'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['skinSelect'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['board_id'].'</div>';
                                echo '<div class="list-col col-custom-auto text-center">'.$val['board_name'].'</div>';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="text" name="listData[read_point]['.$key.']" value="'.$val['read_point'].'" data-proto="'.$val['read_point'].'" class="frm_input frm_full text-center">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="text" name="listData[write_point]['.$key.']" value="'.$val['write_point'].'" data-proto="'.$val['write_point'].'" class="frm_input frm_full text-center">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="text" name="listData[comment_point]['.$key.']" value="'.$val['comment_point'].'" data-proto="'.$val['comment_point'].'" class="frm_input frm_full text-center">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="text" name="listData[download_point]['.$key.']" value="'.$val['download_point'].'" data-proto="'.$val['download_point'].'" class="frm_input frm_full text-center">';
                                echo '</div>';
                                echo '<div class="list-col col-custom-200 text-center">';
                                    echo '<a href="/admin/boardadmin/boardform/'.$val['board_id'].'" class="btn btn-ssm btn-fill-accent">수정</a>';
                                    echo '<a href="" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
                                    echo '<a href="/admin/board/'.$val['board_id'].'/list" class="btn btn-ssm btn-fill-darkgray ml-1">목록</a>';
                                    echo '<a href="/admin/board/'.$val['board_id'].'/write" class="btn btn-ssm btn-fill-darkgray ml-1">글쓰기</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                        $num--;
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
App.registerCallback('updateBoardListDelete', function(data) {
    document.location.reload();
});

App.registerCallback('updateBoardListModify', function(data) {
    document.location.reload();
});
</script>