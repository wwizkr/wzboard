<form name="frm" id="frm">
<input type="hidden" name="article_no" id="article_no" value="<?= $articleData['no'] ?? ''; ?>">
<input type="hidden" name="board_id" id="board_id" value="<?= $boardId; ?>">
<div class="page-container">
    <h2 class="page-title board-title"><?= $boardConfig['board_name'];?></h2>
    <div class="table-container">
        <div class="table-form board-write-form">
            <?php if (empty($memberData)) { ?>
            <div class="table-row mb-3">
                <div class="form-input flex-auto-2">
                    <label for="nickname" class="sound-only">이름</label>
                    <input type="text" name="formData[nickName]" value="" id="nickname" class="require" placeholder="이름"  data-type="text" data-message="이름을 입력해 주세요">
                </div>
                <div class="form-input flex-auto-2">
                    <label for="password" class="sound-only">비밀번호</label>
                    <input type="password" name="formData[password]" value="" id="password" class="require" placeholder="비밀번호" data-type="text" data-message="비밀번호를 입력해 주세요">
                </div>
            </div>
            <?php } ?>
            <div class="table-row mb-3">
                <?php if (!empty($boardsCategory)) { ?>
                <div class="form-input flex-auto-0">
                    <label for="category_no" class="sound-only">카테고리 선택</label>
                    <select name="formData[category_no]" id="category_no" class="require" data-type="select" data-message="카테고리를 선택해 주세요!">
                        <option value="">카테고리선택</option>
                        <?php
                        foreach($boardsCategory as $key=>$val) {
                            echo '<option value="'.$val['no'].'">'.$val['category_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php }?>
                <div class="form-input flex-auto-1">
                    <label for="title" class="sound-only">제목</label>
                    <input type="text" name="formData[title]" value="" id="title" class="require" placeholder="제목" data-type="text" data-message="제목을 입력해 주세요">
                </div>
            </div>
            <div class="table-row mb-3">
                <div class="form-input flex-auto-1">
                    <label for="content" class="sound-only">내용</label>
                    <textarea name="formData[content]" id="content" class="editor-form require" data-toolbar="basic" data-menubar="false" data-height="500" data-type="text" data-message="내용을 입력해 주세요"></textarea>
                </div>
            </div>
            <!-- LINK -->
            <?php if ($boardConfig['is_use_link'] > 0) { ?>
            <?php for($i = 0; $i < $boardConfig['is_use_link']; $i++) { ?>
            <div class="table-row link-row mb-3">
                <div class="form-input form-link flex-auto-1">
                    <label for="link_<?= $i; ?>" class="sound-only">연결주소</label>
                    <input type="hidden" name="linkData[<?= $i; ?>][no]" value="<?= $articleLink[$i]['no'] ?? ''; ?>">
                    <input type="text" name="linkData[<?= $i; ?>][url]" value="<?= $articleLink[$i]['link'] ?? ''; ?>" id="link_<?= $i; ?>" class="label-input">
                    <div class="form-input-icon"><span class="svg-icon svg-icon-link"></span></div>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
            <!-- LINK -->
            <!-- FILE -->
            <?php if ($boardConfig['is_use_file'] > 0) { ?>
            <?php for($i = 0; $i < $boardConfig['is_use_file']; $i++) { ?>
            <div class="table-row file-row file-wrap mb-3">
                <div class="form-input form-file frm-file flex-auto-1">
                    <label for="file_<?= $i; ?>" class="label-file">
                        <span class="file-content file-name">파일선택</span>
                        <span class="file-icon"><span class="svg-icon svg-icon-file"></span></span>
                    </label>
                    <input type="file" name="fileData[<?= $i; ?>]" value="" id="file_<?= $i; ?>" class="file-data">
                </div>
                <?php if (isset($articleFile[$i])) { ?>
                <div class="form-input form-file frm-file flex-auto-0">
                    <input type="hidden" name="oldFilesNo[<?= $i; ?>]" value="<?= $articleFile[$i]['no']; ?>">
                    <label for="old_file_<?= $i; ?>" class="label-file-text"><?= $articleFile[$i]['filename']; ?> 삭제</label>
                    <input type="checkbox" name="oldFilesDel[<?= $i; ?>]" id="old_file_<?= $i; ?>" value="<?= $articleFile[$i]['no']; ?>">
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php } ?>
            <!-- FILE -->
        </div>
        <div class="table-button justify-center">
            <a href="/board/<?= $boardId; ?>/list" class="btn btn-outline-gray mr-1">목록</a>
            <button type="button" value="확인" class="btn btn-outline-hover-gray-accent" onclick="handleAjaxFormSubmit(this);" data-target="/board/<?= $boardId;?>/update" data-callback="successUpdateWrite">확인</button>
        </div>
    </div>
</div>
</form>
<?= $editorScript; ?>
<script>
const articleData = <?php echo json_encode($articleData); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(articleData, 'formData');

    document.addEventListener('click', function (event) {
    });
});

App.registerCallback('successUpdateWrite', function(data) {
    if (data.result === 'success') {
        document.location.href = data.view;
    }
});
</script>