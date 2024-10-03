<form name="frm" id="frm">
<input type="hidden" name="article_no" id="article_no" value="<?= $articleData['no'] ?? ''; ?>">
<input type="hidden" name="board_id" id="board_id" value="<?= $boardId; ?>">
<div class="page-container">
    <h2 class="page-title board-title"><?= $boardConfig['board_name'];?></h2>
    <div class="table-container">
        <div class="table-form board-write-form">
            <?php if (empty($memberData)) { ?>
            <div class="table-row mb-3">
                <input type="text" name="formData[nickName]" value="" id="nickname" class="require" placeholder="이름"  data-type="text" data-message="이름을 입력해 주세요">
            </div>
            <div class="table-row mb-3">
                <input type="password" name="formData[password]" value="" id="password" class="require" placeholder="비밀번호" data-type="text" data-message="비밀번호를 입력해 주세요">
            </div>
            <?php } ?>
            <div class="table-row mb-3">
                <?php if (!empty($boardsCategory)) { ?>
                <select name="formData[category_no]" id="category_no" class="require" data-type="select" data-message="카테고리를 선택해 주세요!">
                    <option value="">카테고리선택</option>
                    <?php
                    foreach($boardsCategory as $key=>$val) {
                        echo '<option value="'.$val['no'].'">'.$val['category_name'].'</option>';
                    }
                    ?>
                </select>
                <?php }?>
                <input type="text" name="formData[title]" value="" id="title" class="require" placeholder="제목" data-type="text" data-message="제목을 입력해 주세요">
            </div>
            <div class="table-row mb-3">
                <textarea name="formData[content]" id="content" class="editor-form require" data-toolbar="basic" data-menubar="true" data-height="500" data-type="text" data-message="내용을 입력해 주세요"></textarea>
            </div>
        </div>
        <div class="table-button justify-center">
            <a href="/board/<?= $boardId; ?>/list" class="btn btn-outline-gray mr-1">목록</a>
            <button type="button" value="확인" class="btn btn-outline-hover-gray-accent btn-form-submit-ajax" data-target="/board/<?= $boardId;?>/update" data-callback="successUpdateWrite">확인</button>
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
    console.log(data);
    alert(data.message);
});
</script>