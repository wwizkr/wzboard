<form name="flist" id="flist">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/members/memberListModify" data-callback="updateMembersModify">선택수정</a>
            <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/members/memberListDelete" data-callback="updateMembersDelete">선택삭제</a>
            <a href="/admin/members/add" class="btn btn-fill-accent">회원 등록</a>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="table-container">
        <div class="table-list-container">
            <ul class="table-list-wrapper">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 text-center">선택</div>
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-140 text-center">회원등급</div>
                        <div class="list-col col-custom-160 text-center">회원아이디</div>
                        <div class="list-col col-custom-120 text-center">회원명</div>
                        <div class="list-col col-custom-160 text-center">회원연락처</div>
                        <div class="list-col col-custom-auto text-center">회원이메일</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($memberData)) {
                    foreach($memberData as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['mb_no'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-140 text-center">';
                                    echo '<select name="member_level['.$key.']" class="form-select" data-proto="'.$val['member_level'].'">';
                                    if(!empty($levelData)) {
                                        foreach($levelData as $lk=>$lv) {
                                            $_selected = $lv['level_id'] == $val['member_level'] ? 'selected' : '';
                                            echo '<option value="'.$lv['level_id'].'" '.$_selected.'>'.$lv['level_name'].'</option>';
                                        }
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-160 text-center">'.$val['mb_id'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['nickName'].'</div>';
                                echo '<div class="list-col col-custom-160 text-center">'.$val['phone'].'</div>';
                                echo '<div class="list-col col-custom-auto col text-center">'.$val['email'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">관리</div>';
                            echo '</div>';
                        echo '</li>';
                        $num--;
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <?= $this->renderPagination($paginationData); ?>
</div>
</form>

<script>
function updateMembersModify(data) {
    console.log(data);
}

function updateMembersDelete(data) {
    console.log(data);
}
</script>