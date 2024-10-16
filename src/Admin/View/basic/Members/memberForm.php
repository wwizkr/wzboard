<!-- 폼 컨테이너들 -->
<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="mbNo" value="<?= $memberData['mb_no']; ?>">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?= $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/members/memberUpdate" data-callback="updateMember">확인</button>
            </div>
        </div>
    </div>
    
    <div class="table-form">
        <h2 class="form-title">회원 정보</h2>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>아이디</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[mb_id]" id="mb_id" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>회원권한</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <?= $levelSelect; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>이름</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[userName]" id="userName" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>닉네임</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[nickName]" id="nickName" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>연락처</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[phone]" id="phone" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>이메일</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[email]" id="email" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<script>
var memberData = <?= json_encode($memberData); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(memberData, 'formData', fillFormLayout);
});

function fillFormLayout() {
}

App.registerCallback('updateMember', function(data) {
    if (data.message) {
        alert(data.message);
    }
});
</script>