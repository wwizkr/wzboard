<form name="frm" id="frm">
<input type="hidden" name="no" id="no" value="">
<input type="hidden" name="boardId" id="boardId" value="<?= $boardId; ?>">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/board/<?= $boardId;?>/update" class="btn-form-submit-ajax">확인</a>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <div class="p-3 table-form">
            <textarea name="content" id="content" class="editor-form"></textarea>
        </div>
    </div>
</div>
</form>
<script src="/assets/js/lib/editor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
<script src="/assets/js/lib/editor/tinymce/tinymce.editor.js"></script>