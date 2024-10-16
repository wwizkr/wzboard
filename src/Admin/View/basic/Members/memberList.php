<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?php echo number_format($totalItems); ?></b> 건</span>
            </span>
            <?= $searchSelectBox['pagenum'] ?>
            <?= $searchSelectBox['member_level'] ?>
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
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/members/memberListModify" data-callback="updateMemberListModify">선택수정</a>
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/members/memberListDelete" data-callback="updateMemberListDelete">선택삭제</a>
                <a href="/admin/members/add" class="btn btn-fill-accent">회원 등록</a>
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
                        <div class="list-col col-custom-120 text-center">회원등급</div>
                        <div class="list-col col-custom-160 text-center">회원아이디</div>
                        <div class="list-col col-custom-120 text-center">회원명</div>
                        <div class="list-col col-custom-160 text-center">회원연락처</div>
                        <div class="list-col col-custom-auto text-center">회원이메일</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($memberList)) {
                    foreach($memberList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['mb_no'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['levelSelect'].'</div>';
                                echo '<div class="list-col col-custom-160 text-center">'.$val['mb_id'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['nickName'].'</div>';
                                echo '<div class="list-col col-custom-160 text-center">'.$val['phone'].'</div>';
                                echo '<div class="list-col col-custom-auto col text-center">'.$val['email'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">';
                                    echo '<a href="/admin/members/memberForm/'.$val['mb_no'].$queryString.'" class="btn btn-ssm btn-fill-accent">수정</a>';
                                    echo '<a href="javascript:void(0);" onclick="confirmDeleteBefore(this);" data-target="/admin/members/memberItemDelete" data-no="'.$val['mb_no'].'" data-callback="updateMemberDelete" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
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
App.registerCallback('updateMemberListDelete', function(data) {
    document.location.reload();
});

App.registerCallback('updateMemberListModify', function(data) {
    document.location.reload();
});
</script>