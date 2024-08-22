<div class="page-container container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 order-2 order-md-1 mb-3 mb-md-0 table-container">
            <h2>목록</h2>
            <div class="p-3 table-list table-list-md">
                <ul class="list-group">
                    <li class="list-group-item list-group-head">
                        <div class="row list-group-row">
                            <div class="col-1 list-group-col text-center">번호</div>
                            <div class="col-3 list-group-col text-center">그룹아이디</div>
                            <div class="col list-group-col text-center">그룹명</div>
                            <div class="col-3 list-group-col list-group-button text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if(!empty($groupData)) {
                        $num = count($groupData);
                        foreach($groupData as $key=>$val) {
                            echo '<li class="list-group-item list-group-body">';
                                echo '<div class="row list-group-row">';
                                    echo '<div class="col-1 list-group-col text-center">'.$num.'</div>';
                                    echo '<div class="col-3 list-group-col text-center">'.$val['group_id'].'</div>';
                                    echo '<div class="col list-group-col text-center">'.$val['group_name'].'</div>';
                                    echo '<div class="col-3 list-group-col list-group-button text-center">관리</div>';
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
                        <label for="group_id" class="form-label">그룹명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <input type="text" name="formData[group_name]" value="" class="form-control" id="group_name">
                    </div>
                </div>
                <div class="table-row row mb-3">
                    <div class="table-th col-md-4">
                        <label for="allow_level" class="form-label">접근가능레벨</label>
                    </div>
                    <div class="table-td col-md-8">
                        <select name="formData[allow_level]" class="form-control" id="allow_level">
                            <option value="0">비회원 접근가능</option>
                            <?php
                            foreach($levelData as $key=>$val) {
                                echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/boards/groupUpdate">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>