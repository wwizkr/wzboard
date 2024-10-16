<div class="page-container">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn"></div>
        </div>
    </div>
    <div class="table-flex flex-wrap table-container">
        <div class="col-12 col-md-8 order-2 order-md-1 mb-md-0">
            <h2 class="form-title">목록</h2>
            <div class="table-list-container">
                <form name="flist" id="flist">
                <ul class="table-list-wrapper">
                    <li class="table-list-row list-head">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">번호</div>
                            <div class="list-col col-custom-100 text-center">카테고리명</div>
                            <div class="list-col col-custom-auto text-center">카테고리설명</div>
                            <div class="list-col col-custom-100 text-center">접근레벨</div>
                            <div class="list-col col-custom-100 text-center">읽기레벨</div>
                            <div class="list-col col-custom-100 text-center">쓰기레벨</div>
                            <div class="list-col col-custom-100 text-center">댓글레벨</div>
                            <div class="list-col col-custom-100 text-center">다운로드레벨</div>
                            <div class="list-col col-custom-80 text-center">정렬순서</div>
                            <div class="list-col col-custom-100 list-group-button text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if(!empty($categoryList)) {
                        $num = count($categoryList);
                        foreach($categoryList as $key=>$val) {
                            echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                                echo '<div class="list-row">';
                                    echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['category_name'].'</div>';
                                    echo '<div class="list-col col-custom-auto text-left">'.$val['category_desc'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['list_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['read_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['write_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['comment_level'].'</div>';
                                    echo '<div class="list-col col-custom-100 text-center">'.$val['levelSelect']['download_level'].'</div>';
                                    echo '<div class="list-col col-custom-80 text-center">';
                                        echo '<div class="frm-input-row justify-center">';
                                            echo '<div class="frm-input wfpx-40">';
                                                echo '<input type="text" name="listData[order_num]['.$key.']" value="'.$val['order_num'].'" class="frm_input frm_full text-center" data-proto="'.$val['order_num'].'">';
                                            echo '</div>';
                                        echo '</div>';
                                    echo '</div>';
                                    echo '<div class="list-col col-custom-100 list-group-button text-center">';
                                        echo '<button type="button" class="btn btn-ssm btn-fill-accent" data-data=\''.json_encode($val).'\' onclick="loaderData(this);">수정</button>';
                                        echo '<a href="javascript:void(0);" onclick="confirmDeleteBefore(this);" data-target="/admin/boardadmin/boardCategoryDelete" data-no="'.$val['no'].'" data-callback="boardCategoryDelete" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</li>';
                            $num--;
                        }
                    }
                    ?>
                </ul>
                </form>
            </div>
        </div>
        <div class="col-12 col-md-4 order-1 order-md-2 mb-md-0 table-container px-3">
            <form name="frm" id="frm">
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="category_no" value="" id="category_no">
            <div class="table-form table-form-md">
                <h2 class="form-title">카테고리 등록/수정</h2>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="category_name" class="form-label">카테고리명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-120">
                                <input type="text" name="formData[category_name]" value="" class="frm_input frm_full require" id="category_name" data-type="text" data-msg="카테고리명을" data-regex="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="category_desc" class="form-label">카테고리 설명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input frm-input-full">
                                <input type="text" name="formData[category_desc]" value="" class="frm_input frm_full require" id="category_desc" data-type="text" data-msg="카테고리명을" data-regex="">
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
                        <label for="order_num" class="form-label">정렬순서</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-80">
                                <input type="text" name="formData[order_num]" value="" class="frm_input frm_full" id="order_num">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-flex justify-end mt-3">
                    <button type="button" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/boardadmin/categoryUpdate" data-callback="updateProcess">확인</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
function loaderData(button) {
    var data = JSON.parse(button.getAttribute('data-data'));
    console.log(data);
    document.getElementById('action').value = 'update';
    document.getElementById('category_no').value = data.no;
    fillFormData(data, 'formData');
}

App.registerCallback('updateProcess', function(data) {
    console.log(data);
    location.reload();
});

App.registerCallback('boardCategoryDelete', function(data) {
    console.log(data);
    location.reload();
});
</script>