<!-- 폼 컨테이너들 -->
<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="ctId" value="<?= $ctId;?>">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?= $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/settings/clauseItemUpdate" data-callback="updateClauseItem">확인</button>
            </div>
        </div>
    </div>
    
    <div class="table-form">
        <h2 class="form-title">홈페이지 정보</h2>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>페이지 분류</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <?= $clauseTypeCheckBox; ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>약관 분류</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <?= $clauseKindSelect; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>약관 아이디</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[ct_page_id]" id="ct_page_id" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>약관 제목</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpe-60">
                        <input type="text" name="formData[ct_subject]" id="ct_subject" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>약관 내용</span>
            </div>
            <div class="table-td col-md-10">
                <div class="alert alert-info">
                    <ul>
                        <li>{#회사명} => "회사명"</li>
                        <li>{#홈페이지} => "홈페이지 주소"</li>
                        <li>{#대표자} => "대표자명"</li>
                        <li>{#책임자} => "개인정보 보호 책임자"</li>
                        <li>{#전화번호} => "회사 대표전화번호"</li>
                        <li>{#이메일} => "회사 대표이메일"</li>
                        <li>{#등록일자} => "약관 등록 일자"</li>
                        <li>{#적용일자} => "약관 등록 일자"</li>
                        <li>{#사이트명} => "홈페이지 명"</li>
                    </ul>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[ct_content]" id="ct_content" class="editor-form require" data-toolbar="basic" data-menubar="true" data-height="500" data-type="text" data-message="내용은 필수입니다."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>출력순서</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-80"><input type="text" name="formData[ct_order]" id="ct_order" value="" class="frm_input frm_full"></div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>사용여부</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                    <?= $clauseUseSelect; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<?= $editorScript; ?>
<script>
var clauseItem = <?= json_encode($clauseItem); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(clauseItem, 'formData', fillFormLayout);
});

function fillFormLayout() {
}

App.registerCallback('updateClauseItem', function(data) {
    if (data.message) {
        alert(data.message);
    }
});
</script>