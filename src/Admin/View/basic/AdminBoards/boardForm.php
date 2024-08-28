<!-- 폼 컨테이너들 -->
<form name="frm" id="frm">
<input type="hidden" name="action" value="<?php echo $action; ?>">
<input type="hidden" name="board_no" value="<?php echo $selectBoard['no'] ?? 0 ; ?>">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <button type="button" value="확인" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/boards/boardUpdate">확인</button>
        </div>
    </div>
</div>
<div class="page-container">
    <h2>게시판 생성</h2>
    <div id="anc_cf_basic" class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="group_no" class="form-label">게시판 그룹선택</label>
            </div>
            <div class="table-td col-md-10">
                <select name="formData[group_no]" id="group_no" class="form-select require" data-type="select" data-msg="그룹을 선택해 주세요!">
                    <option value="">게시판 그룹선택</option>
                    <?php
                    foreach($groupData as $key=>$val) {
                        echo '<option value="'.$val['no'].'">'.$val['group_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="board_id" class="form-label">게시판 ID</label>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[board_id]" id="board_id" class="form-control me-2 require" data-type="text" data-message="게시판아이디는 필수입니다." data-regex="^[a-zA-Z0-9_]+$" placeholder="게시판 ID" style="max-width: 160px;">
                    <button type="button" class="btn btn-primary">중복검사</button>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="board_name" class="form-label">게시판 명</label>
            </div>
            <div class="table-td col-md-10">
                <input type="text" name="formData[board_name]" id="board_name" class="form-control me-2 require" data-type="text" data-message="게시판명은 필수입니다." data-regex="" placeholder="게시판명">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="category_<?php echo $categoryData[0]['no'];?>" class="form-label">게시판 카테고리</label>
            </div>
            <div class="table-td col-md-10" id="board_category">
            <?php
            if (!empty($categoryData)) {
                foreach ($categoryData as $category) {
                    echo '<div class="form-check form-check-inline">';
                        echo '<input class="form-check-input" type="checkbox" name="formData[categories][]" id="category_'.$category['no'].'" value="'.$category['no'].'">';
                        echo '<label class="form-check-label" for="category_'.$category['no'].'">';
                            echo htmlspecialchars($category['category_name']);
                        echo '</label>';
                    echo '</div>';
                }
            } else {
                echo '<p>카테고리가 없습니다.</p>';
            }
            ?>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="board_skin" class="form-label">게시판 스킨</label>
            </div>
            <div class="table-td col-md-10">
                <select name="formData[board_skin]" id="board_skin" class="form-select">
                <?php
                foreach($skinData as $key=>$val) {
                    echo '<option value="'.$val['name'].'">'.$val['name'].'</option>';
                }
                ?>
                </select>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="read_level" class="form-label">게시판 읽기</label>
            </div>
            <div class="table-td col-md-10">
                <select name="formData[read_level]" id="read_level" class="form-select">
                    <option value="0">비회원</option>
                    <?php
                    foreach($levelData as $key=>$val) {
                        echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="write_level" class="form-label">게시판 쓰기</label>
            </div>
            <div class="table-td col-md-10">
                <select name="formData[write_level]" id="write_level" class="form-select">
                    <option value="0">비회원</option>
                    <?php
                    foreach($levelData as $key=>$val) {
                        echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="download_level" class="form-label">게시판 다운로드</label>
            </div>
            <div class="table-td col-md-10">
                <select name="formData[download_level]" id="download_level" class="form-select">
                    <option value="0">비회원</option>
                    <?php
                    foreach($levelData as $key=>$val) {
                        echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</div>
</form>