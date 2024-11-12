<!-- 폼 컨테이너들 -->
<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="memberNo" value="<?= $memberData['mb_no'] ?? ''; ?>">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?= $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/members/memberUpdate" data-beforeSubmit="" data-callback="updateMember">확인</button>
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
                        <input type="text" name="formData[mb_id]" id="mb_id" value="" class="frm_input frm_full" <?= !empty($memberData['mb_no']) ? 'readonly' : ''; ?>>
                    </div>
                    <?php if (empty($memberData['mb_no'])) { ?>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-fill-colorgreen" onclick="checkValidate(this, 'userid');">중복확인</button>
                    </div>
                    <?php } ?>
                </div>
                <?php if (empty($memberData['mb_no'])) { ?>
                <div class="validate-message" data-message="아이디 중복확인을 해주세요."></div>
                <?php } ?>
            </div>
            <div class="table-th col-md-2">
                <span>비밀번호</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="password" name="formData[password]" id="passowrd" value="" class="frm_input frm_full">
                    </div>
                    <?php if (isset($memberData['mb_no']) && $memberData['mb_no']) { ?>
                    <div class="frm-guide">
                        <span>비밀번호 변경 시에만 입력해 주세요</span>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>회원권한</span>
            </div>
            <div class="table-td col-md-10">
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
                        <input type="text" name="formData[phone]" id="phone" value="" class="frm_input frm_full mask-hp">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>이메일</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-260">
                        <input type="text" name="formData[email]" id="email" value="" class="frm_input frm_full">
                    </div>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-fill-colorgreen" onclick="checkValidate(this, 'email');">중복확인</button>
                    </div>
                </div>
                <div class="validate-message" <?= empty($memberData['mb_no']) ? 'data-require="1"' : '';?> data-message="이메일 중복확인을 해주세요."></div>
            </div>
        </div>
    </div>
    </form>
</div>
<script>
var memberData = <?= json_encode($memberData); ?>;
console.log(memberData.mb_no);
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(memberData, 'formData', fillFormLayout);
});

function fillFormLayout() {
}

async function checkValidate(el, field) {
    if (!field) {
        return false;
    }
    
    let value = '';
    let fieldName = '';
    let validateField = '';
    if (field === 'userid') {
        validateField = document.getElementById('mb_id');
        value =validateField.value;
        fieldName = '아이디가';
    }

    if (field === 'email') {
        validateField = document.getElementById('email');
        value =validateField.value;
        fieldName = '이메일이';
    }

    if (!value) {
        alert(fieldName + ' 입력되지 않았습니다.');
        return false;
    }
    
    let data = {value:value};
    let url = '/admin/members/validate/'+field;

    const parentRow = el.closest('.table-td');
    const messageElement = parentRow.querySelector('.validate-message');

    try {
        const result = await sendCustomAjaxRequest('POST', url, data);
        
        let html = '<span style="color:var(--colorblue);">'+ result.message +'</span>';
        if (result.result === 'failure') {
            html = '<span style="color:var(--colorred);">'+ result.message +'</span>';
            validateField.value = '';
        }
        
        messageElement.innerHTML = html;
    }
    catch (error) {
        alert('검색에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
    }
}

App.registerCallback('updateMember', function(data) {
    if (data.message) {
        alert(data.message);
    }

    if (typeof memberData.mb_no !== 'undefined' && memberData.mb_no !== null) {
        location.reload();
    } else {
        location.href = '/admin/members/memberForm/' + data.data.memberNo;
    }
});
</script>