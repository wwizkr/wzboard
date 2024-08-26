<form name="flist" id="flist">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/members/add">회원 등록</a>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <h2>목록</h2>
        <div class="p-3 table-list table-list table-list-scroll">
            <ul class="list-group">
                <li class="list-group-item list-group-head">
                    <div class="row list-group-row">
                        <div class="col-custom-60 list-group-col text-center">번호</div>
                        <div class="col-custom-140 list-group-col text-center">회원등급</div>
                        <div class="col-custom-120 list-group-col text-center">회원아이디</div>
                        <div class="col-custom-120 list-group-col text-center">회원명</div>
                        <div class="col-custom-160 list-group-col text-center">회원연락처</div>
                        <div class="col list-group-col text-center">회원이메일</div>
                        <div class="col-custom-100 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($memberData)) {
                    foreach($memberData as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - $key;
                        echo '<li class="list-group-item list-group-body">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-custom-60 list-group-col text-center">'.$num.'</div>';
                                echo '<div class="col-custom-140 list-group-col text-center">';
                                    echo '<select name="member_level['.$key.']" class="form-select" data-proto="'.$val['member_level'].'">';
                                    if(!empty($levelData)) {
                                        foreach($levelData as $lk=>$lv) {
                                            $_selected = $lv['level_id'] == $val['member_level'] ? 'selected' : '';
                                            echo '<option value="'.$lv['level_id'].'" '.$_selected.'>'.$lv['level_name'].'</option>';
                                        }
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">'.$val['mb_id'].'</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">'.$val['nickName'].'</div>';
                                echo '<div class="col-custom-160 list-group-col text-center">'.$val['phone'].'</div>';
                                echo '<div class="col list-group-col text-center">'.$val['email'].'</div>';
                                echo '<div class="col-custom-100 list-group-col list-group-button text-center">관리</div>';
                            echo '</div>';
                        echo '</li>';
                        $num--;
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <?php if (isset($paginationData)): ?>
        <?= $this->renderPagination($paginationData) ?>
    <?php endif; ?>
</div>
</form>