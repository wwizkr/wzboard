<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?php echo number_format(count($levelData)); ?></b> 건</span>
            </span>
        </div>
        <div class="local-auto">
            
        </div>
    </div>
    </form>
    <form name="flist" id="flist" method="post">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="javascript:void(0)" class="btn btn-fill-gray" onclick="handleAjaxFormSubmit(this);" data-target="/admin/members/memberLevelModify" data-callback="updateMemberLevelModify">선택수정</a>
            </div>
        </div>
    </div>
    <div class="table-container">
        <div class="table-list-container">
            <ul class="table-list-wrapper">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-80 text-center">사용여부</div>
                        <div class="list-col col-custom-80 text-center">회원등급</div>
                        <div class="list-col col-custom-160 text-center">회원분류</div>
                        <div class="list-col col-custom-160 text-center">회원등급 명</div>
                        <div class="list-col col-custom-80 text-center">회원수</div>
                        <div class="list-col col-custom-100 text-center">자동등업</div>
                        <div class="list-col col-custom-100 text-center">등업적립금</div>
                        <div class="list-col col-custom-100 text-center">쇼핑적립률</div>
                        <div class="list-col col-custom-500 list-rowspan">
                            <div class="list-sub"><span>등업조건</span></div>
                            <div class="list-sub">
                                <span class="wfpx-100">최소적립금</span>
                                <span class="wfpx-100">게시글작성수</span>
                                <span class="wfpx-100">댓글작성수</span>
                                <span class="wfpx-100">로그인 횟수</span>
                                <span class="wfpx-100">가입일</span>
                            </div>
                        </div>
                        <div class="list-col col-custom-auto text-center">레벨설명</div>
                    </div>
                </li>
                <?php
                foreach($levelData as $key=>$val) {
                    $super_readonly = $val['is_super'] == 1 ? 'readonly' : '';
                    $admin_readonly = $val['is_admin'] == 1 ? 'readonly' : '';
                    echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                        echo '<div class="list-row">';
                            echo '<div class="list-col col-custom-80 text-center">';
                                echo '<input type="hidden" name="level[]" value="'.$val['level_id'].'">';
                                echo '<input type="checkbox" name="level_use['.$val['level_id'].']" value="1"'.($val['level_use'] == '1' ? 'checked' : '').' '.$super_readonly.'>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-80 text-center">'.$val['level_id'].'</div>';
                            echo '<div class="list-col col-custom-160 text-center">';
                                echo '<select name="is_admin['.$val['level_id'].']" data-proto="'.$val['is_admin'].'" '.$super_readonly.'>';
                                    echo '<option value="1" '.(($val['is_admin'] == 1) ? 'selected' : '').'>관리자</option>';
                                    echo '<option value="0" '.(($val['is_admin'] == 0) ? 'selected' : '').'>회원</option>';
                                echo '</select>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-160 text-center">';
                                echo '<input type="text" name="level_name['.$val['level_id'].']" value="'.$val['level_name'].'" class="frm_input frm_full" data-proto="'.$val['level_name'].'">';
                            echo '</div>';
                            echo '<div class="list-col col-custom-80 text-center">회원수</div>';
                            echo '<div class="list-col col-custom-100 text-center">';
                                echo '<div class="frm-input-row justify-center">';
                                echo '<input type="checkbox" name="auto_level_up['.$val['level_id'].']" id="auto_level_up_'.$val['level_id'].'" value="1" data-proto="'.$val['level_id'].'" '.(($val['auto_level_up'] == 1) ? 'checked' : '').'>';
                                echo '<label for="auto_level_up_'.$val['level_id'].'" class="ml-1">적용</label>';
                                echo '</div>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-100 text-center">';
                                echo '<input type="text" name="level_up_point['.$val['level_id'].']" value="'.$val['level_up_point'].'" data-proto="'.$val['level_up_point'].'" class="frm_input mask-num" '.$admin_readonly.'>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-100 text-center">';
                                echo '<input type="text" name="purchase_amount['.$val['level_id'].']" value="'.$val['purchase_amount'].'" data-proto="'.$val['purchase_amount'].'" class="frm_input mask-num decimal-2 text-right" '.$admin_readonly.'>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-500 list-rowspan">';
                                echo '<div class="list-sub">';
                                    echo '<span class="wfpx-100"><input type="text" name="min_point['.$val['level_id'].']" value="'.$val['min_point'].'" data-proto="'.$val['min_point'].'" class="frm_input mask-num text-right" '.$admin_readonly.'></span>';
                                    echo '<span class="wfpx-100"><input type="text" name="min_posts['.$val['level_id'].']" value="'.$val['min_posts'].'" data-proto="'.$val['min_posts'].'" class="frm_input mask-num text-right" '.$admin_readonly.'></span>';
                                    echo '<span class="wfpx-100"><input type="text" name="min_comments['.$val['level_id'].']" value="'.$val['min_comments'].'" data-proto="'.$val['min_comments'].'" class="frm_input mask-num text-right" '.$admin_readonly.'></span>';
                                    echo '<span class="wfpx-100"><input type="text" name="min_login_count['.$val['level_id'].']" value="'.$val['min_login_count'].'" data-proto="'.$val['min_login_count'].'" class="frm_input mask-num text-right" '.$admin_readonly.'></span>';
                                    echo '<span class="wfpx-100"><input type="text" name="min_days_join['.$val['level_id'].']" value="'.$val['min_days_join'].'" data-proto="'.$val['min_days_join'].'" class="frm_input mask-num text-right" '.$admin_readonly.'></span>';
                                echo '</div>';
                            echo '</div>';
                            echo '<div class="list-col col-custom-auto text-center">';
                                echo '<input type="text" name="description['.$val['level_id'].']" value="'.$val['description'].'" class="frm_input frm_full" data-proto="'.$val['description'].'">';
                            echo '</div>';
                        echo '</div>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    </form>
</div>


<script>
App.registerCallback('updateMemberLevelModify', function(data) {
    alert(data.message);
    document.location.reload();
});
</script>