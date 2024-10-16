<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="board_no" id="board_no" value="<?php echo $boardConfig['no'] ?? 0 ; ?>">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="/admin/boardadmin/boards" class="btn btn-fill-darkgray">목록</a>
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/boardadmin/boardUpdate">확인</button>
            </div>
        </div>
    </div>

    <div class="table-form">
        <h2 class="form-title">게시판 설정</h2>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="group_no" class="form-label">게시판 그룹선택</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <select name="formData[group_no]" id="group_no" class="frm_input frm_full require" data-type="select" data-msg="그룹을 선택해 주세요!">
                            <option value="">게시판 그룹선택</option>
                            <?php
                            foreach($groupData as $key=>$val) {
                                echo '<option value="'.$val['no'].'">'.$val['group_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="board_id" class="form-label">게시판 ID</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[board_id]" id="board_id" class="frm_input frm_full require" data-type="text" data-message="게시판아이디는 필수입니다." data-regex="^[a-zA-Z0-9_]+$" placeholder="게시판 ID" style="max-width: 160px;" <?php echo !empty($boardConfig) ? 'readonly' : ''; ?>>
                    </div>
                    <?php if(empty($boardConfig)) { ?>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-outline-hover-gray">중복검사</button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="board_name" class="form-label">게시판 명</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[board_name]" id="board_name" class="frm_input frm_full require" data-type="text" data-message="게시판명은 필수입니다." data-regex="" placeholder="게시판명">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="category_<?php echo $categoryData[0]['no'];?>" class="form-label">게시판 카테고리</label>
            </div>
            <div class="table-td col-md-10" id="board_category">
            <?php
            if (!empty($categoryData)) {
                echo '<div class="frm-input-row">';
                foreach ($categoryData as $category) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input class="form-check-input" type="checkbox" name="formData[categories][]" id="category_'.$category['no'].'" value="'.$category['no'].'">';
                        echo '<label class="form-check-label" for="category_'.$category['no'].'">';
                            echo htmlspecialchars($category['category_name']);
                        echo '</label>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>카테고리가 없습니다.</p>';
            }
            ?>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="board_skin" class="form-label">게시판 스킨</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <select name="formData[board_skin]" id="board_skin" class="frm_input frm_full">
                        <?php
                        foreach($skinData as $key=>$val) {
                            echo '<option value="'.$val['name'].'">'.$val['name'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="list_level" class="form-label">게시판 접근</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $levelSelect['list_level']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="read_level" class="form-label">게시판 읽기</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $levelSelect['read_level']; ?>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="write_level" class="form-label">게시판 쓰기</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $levelSelect['write_level']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="comment_level" class="form-label">게시판 댓글쓰기</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $levelSelect['comment_level']; ?>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="download_level" class="form-label">게시판 다운로드</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $levelSelect['download_level']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="write_point" class="form-label">게시판 글쓰기 포인트</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-70">
                        <input type="text" name="formData[write_point]" id="write_point" class="frm_input frm_full text-center">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">차감 시 -로 입력하세요</span>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="read_point" class="form-label">게시판 읽기 포인트</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-70">
                        <input type="text" name="formData[read_point]" id="read_point" class="frm_input frm_full text-center">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">차감 시 -로 입력하세요</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="download_point" class="form-label">게시판 다운로드 포인트</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-70">
                        <input type="text" name="formData[download_point]" id="download_point" class="frm_input frm_full text-center">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">차감 시 -로 입력하세요</span>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="comment_point" class="form-label">게시판 댓글 포인트</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-70">
                        <input type="text" name="formData[comment_point]" id="comment_point" class="frm_input frm_full text-center">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">차감 시 -로 입력하세요</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>게시판 댓글 사용</span>
            </div>
            <div class="table-td col-md-4">
                <?php
                echo '<div class="frm-input-row">';
                foreach (array('사용안함','사용함') as $key=>$val) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input type="radio" name="formData[is_use_comment]" id="is_use_comment_'.$key.'" value="'.$key.'">';
                        echo '<label for="is_use_comment_'.$key.'">'.$val.'</label>';
                    echo '</div>';
                }
                echo '</div>';
                ?>
            </div>
            <div class="table-th col-md-2">
                <span>게시판 파일첨부</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-70">
                        <input type="text" name="formData[is_use_file]" id="is_use_file" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>게시판 리액션(좋아요,싫어요...)</span>
            </div>
            <div class="table-td col-md-10">
                <input type="text" name="formData[is_article_reaction]" id="is_article_reaction" class="frm_input frm_full">
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>댓글 리액션(좋아요,싫어요...)</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[is_comment_reaction]" id="is_comment_reaction" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>게시판 목록 형식</span>
            </div>
            <div class="table-td col-md-10">
                <?php
                echo '<div class="frm-input-row">';
                foreach (array('페이지 이동','더보기 출력') as $key=>$val) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input type="radio" name="formData[board_list_type]" id="board_list_type_'.$key.'" value="'.$key.'">';
                        echo '<label for="board_list_type_'.$key.'">'.$val.'</label>';
                    echo '</div>';
                }
                echo '</div>';
                ?>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
var boardConfig = <?php echo json_encode($boardConfig); ?>;
var boardCategory = <?php echo json_encode($boardCategory); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(boardConfig, 'formData', checkBoardConfig);
});

function checkBoardConfig() {
    // boardCategory의 모든 'no' 값을 배열로 추출
    var boardCategoryNos = boardCategory.map(function(item) {
        return item.no;
    });

    // 체크박스를 선택하고, 해당 값이 boardConfig의 'no'에 포함되어 있는지 확인하여 체크
    document.querySelectorAll('[name="formData[categories][]"]').forEach(function(checkbox) {
        if (boardCategoryNos.includes(parseInt(checkbox.value))) {
            checkbox.checked = true; // 체크박스 체크
        }
    });
}
</script>