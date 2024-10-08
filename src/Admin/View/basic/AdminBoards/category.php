<div class="page-container container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 order-2 order-md-1 mb-3 mb-md-0 table-container">
            <h2>목록</h2>
            <div class="p-3 table-list table-list-md">
                <ul class="list-group">
                    <li class="list-group-item list-group-head">
                        <div class="row list-group-row">
                            <div class="col-1 list-group-col text-center">번호</div>
                            <div class="col-2 list-group-col text-center">카테고리명</div>
                            <div class="col list-group-col text-center">카테고리설명</div>
                            <div class="col-2 list-group-col text-center">접근레벨</div>
                            <div class="col-2 list-group-col text-center">정렬순서</div>
                            <div class="col-2 list-group-col list-group-button text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if(!empty($categoryData)) {
                        $num = count($categoryData);
                        foreach($categoryData as $key=>$val) {
                            echo '<li class="list-group-item list-group-body">';
                                echo '<div class="row list-group-row">';
                                    echo '<div class="col-1 list-group-col text-center">'.$num.'</div>';
                                    echo '<div class="col-2 list-group-col text-center">'.$val['category_name'].'</div>';
                                    echo '<div class="col list-group-col text-center">'.$val['category_desc'].'</div>';
                                    echo '<div class="col-2 list-group-col text-center">'.$val['allow_level'].'</div>';
                                    echo '<div class="col-2 list-group-col text-center">'.$val['order_num'].'</div>';
                                    echo '<div class="col-3 list-group-col list-group-button text-center">';
                                        echo '<button type="button" class="btn btn-sm btn-success" data-data=\''.json_encode($val).'\' onclick="loaderData(this);">수정</button>';
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
        <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0 table-container">
            <h2>입력폼</h2>
            <form name="frm" id="frm">
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="category_no" value="" id="category_no">
            <div class="p-3 table-form table-form-md">
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="category_name" class="form-label">카테고리명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[category_name]" value="" class="form-control require" id="category_name" data-type="text" data-msg="카테고리명을" data-regex="">
                    </div>
                </div>
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="category_desc" class="form-label">카테고리 설명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[category_desc]" value="" class="form-control" id="category_desc">
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
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="order_num" class="form-label">정렬순서</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[order_num]" value="" class="form-control" id="order_num">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/boardadmin/categoryUpdate" data-callback="updateProcess">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
function loaderData(button) {
    var data = JSON.parse(button.getAttribute('data-data'));
    document.getElementById('action').value = 'update';
    document.getElementById('category_no').value = data.no;
    fillFormData(data, 'formData');
}

App.registerCallback('updateProcess', function(data) {
    console.log(data);
    location.reload();
});
</script>