<div class="page-container">
    <div class="table-flex justify-between flex-wrap">
        <div class="col-12 col-md-6 order-2 order-md-1 table-container">
            <h2 class="form-title">목록</h2>
            <div class="table-list-container">
                <ul class="table-list-wrapper">
                    <li class="table-list-row list-head">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">번호</div>
                            <div class="list-col col-custom-120 text-center">그룹아이디</div>
                            <div class="list-col col-custom-auto text-center">그룹명</div>
                            <div class="list-col col-custom-100 text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if(!empty($groupData)) {
                        $num = count($groupData);
                        foreach($groupData as $key=>$val) {
                            echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                                echo '<div class="list-row">';
                                    echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                    echo '<div class="list-col col-custom-120 text-center">'.$val['group_id'].'</div>';
                                    echo '<div class="list-col col-custom-auto text-center">'.$val['group_name'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">';
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
        <div class="col-12 col-md-4 order-1 order-md-2 mb-md-0 table-container">
            <h2 class="form-title">입력폼</h2>
            <form name="frm" id="frm">
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="group_no" value="" id="group_no">
            <div class="p-3 table-form table-form-md">
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="group_id" class="form-label">그룹아이디</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[group_id]" value="" class="form-control" id="group_id">
                    </div>
                </div>
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="group_name" class="form-label">그룹명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[group_name]" value="" class="form-control" id="group_name">
                    </div>
                </div>
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="order_num" class="form-label">정렬순서</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[order_num]" value="" class="form-control" id="order_num">
                    </div>
                </div>
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="allow_level" class="form-label">접근가능레벨</label>
                    </div>
                    <div class="table-td col-md-8">
                        <select name="formData[allow_level]" class="form-select" id="allow_level">
                            <option value="0">비회원 접근가능</option>
                            <?php
                            foreach($levelData as $key=>$val) {
                                echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/boardadmin/groupUpdate" data-callback="updateProcess">Submit</button>
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