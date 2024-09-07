<form name="frm" id="frm">
<input type="hidden" name="article_no" id="article_no" value="<?= $articleData['no'] ?? ''; ?>">
<input type="hidden" name="board_id" id="board_id" value="<?= $boardId; ?>">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <button type="button" value="확인" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/board/<?= $boardId;?>/update" data-callback="updateWrite">확인</button>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <div class="p-3 table-form">
            <?php if(!empty($boardsCategory)) { ?>
            <div class="table-row row mb-3">
                <div class="table-td col-12">
                    <select name="formData[category_no]" id="category_no" class="form-select require" data-type="select" data-msg="카테고리를 선택해 주세요!">
                        <option value="">카테고리선택</option>
                        <?php
                        foreach($boardsCategory as $key=>$val) {
                            echo '<option value="'.$val['no'].'">'.$val['category_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="table-row row mb-3">
                <div class="table-td col-12">
                    <input type="text" name="formData[title]" value="" id="title" class="form-control" placeholder="제목">
                </div>
            </div>
            <div class="table-row mb-3">
                <div class="table-td col-12">
                    <textarea name="formData[content]" id="content" class="editor-form" data-toolbar="basic" data-menubar="true" data-height="500"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<?= $editorScript; ?>
<script>
const articleData = <?php echo json_encode($articleData); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(articleData, 'formData');
});

function updateWrite(data) {
    console.log(data);
    alert(data.message);
}
</script>