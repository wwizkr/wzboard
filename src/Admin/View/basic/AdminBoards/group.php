<div class="page-container">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn"></div>
        </div>
    </div>
    <div class="table-flex flex-wrap table-container">
        <div class="col-12 col-md-8 order-2 order-md-1 table-container">
            <h2 class="form-title">게시판 그룹 목록</h2>
            <div class="table-list-container">
                <ul class="table-list-wrapper">
                    <li class="table-list-row list-head">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">번호</div>
                            <div class="list-col col-custom-120 text-center">그룹관리자</div>
                            <div class="list-col col-custom-120 text-center">그룹아이디</div>
                            <div class="list-col col-custom-auto text-center">그룹명</div>
                            <div class="list-col col-custom-100 text-center">접근레벨</div>
                            <div class="list-col col-custom-100 text-center">읽기레벨</div>
                            <div class="list-col col-custom-100 text-center">쓰기레벨</div>
                            <div class="list-col col-custom-100 text-center">댓글레벨</div>
                            <div class="list-col col-custom-100 text-center">다운로드레벨</div>
                            <div class="list-col col-custom-100 text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if(!empty($groupList)) {
                        $num = count($groupList);
                        foreach($groupList as $key=>$val) {
                            echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                                echo '<div class="list-row">';
                                    echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                    echo '<div class="list-col col-custom-120 text-center">'.$val['group_admin'].'</div>';
                                    echo '<div class="list-col col-custom-120 text-center">'.$val['group_id'].'</div>';
                                    echo '<div class="list-col col-custom-auto text-center">'.$val['group_name'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['list_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['read_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['write_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['comment_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['download_level'].'</div>';
                                    echo '<div class="list-col list-col-row col-custom-100 text-center">';
                                        echo '<button type="button" class="btn btn-ssm btn-fill-accent" data-data=\''.json_encode($val).'\' onclick="loaderData(this);">수정</button>';
                                        echo '<button type="button" class="btn btn-ssm btn-fill-darkgray ml-1" onclick="confirmDeleteBefore(this);" data-target="/admin/boardadmin/boardGroupDelete" data-no="'.$val['no'].'" data-callback="updateGroupDelete" data-message="그룹내에 게시판이 생성되어 있을 경우 삭제가 불가합니다. 삭제하시겠습니까?">삭제</button>';
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
        <div class="col-12 col-md-4 order-1 order-md-2 mb-md-0 table-container px-3">
            <form name="frm" id="frm">
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="group_no" value="" id="group_no">
            <div class="table-form table-form-md">
                <h2 class="form-title">게시판 그룹 등록/수정</h2>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="group_admin">그룹관리자</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-160">
                                <input type="text" name="formData[group_admin]" id="group_admin" value="" class="frm_input frm_full">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="group_id">그룹아이디</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-160">
                                <input type="text" name="formData[group_id]" id="group_id" value="" class="frm_input frm_full">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="group_name">그룹명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-160">
                                <input type="text" name="formData[group_name]" id="group_name" value="" class="frm_input frm_full">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>접근가능레벨</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                            <?= $levelSelect['list_level']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>읽기레벨</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                            <?= $levelSelect['read_level']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>쓰기레벨</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                            <?= $levelSelect['write_level']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>댓글레벨</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                            <?= $levelSelect['comment_level']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>다운로드레벨</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                            <?= $levelSelect['download_level']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="order_num">정렬순서</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-80">
                                <input type="text" name="formData[order_num]" value="" class="frm_input frm_full text-center" id="order_num">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-flex justify-end mt-3">
                    <button type="button" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/boardadmin/groupUpdate" data-callback="updateProcess">확인</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
function loaderData(button) {
    var data = JSON.parse(button.getAttribute('data-data'));
    document.getElementById('action').value = 'update';
    document.getElementById('group_no').value = data.no;
    fillFormData(data, 'formData');
}

App.registerCallback('updateProcess', function(data) {
    console.log(data);
    location.reload();
});
</script>