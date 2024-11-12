<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="programNo" value="<?= $programData['no'] ?? 0; ?>">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?= $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="/adversting/admin/programList?<?= $queryString; ?>" class="btn btn-fill-darkgray">목록</a>
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/adversting/admin/programUpdate" data-callback="updateProgramData">확인</button>
            </div>
        </div>
    </div>
    
    <div class="table-form">
        <h2 class="form-title">프로그램 정보</h2>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>프로그램명</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[companyName]" id="companyName" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>홈페이지(UI) URL</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[siteUrl]" id="siteUrl" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>프로그램 분류</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[programType]" id="programType" value="" class="frm_input frm_full" placeholder="유입플...">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>프로그램 상품종류</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[programItems]" id="programItems" value="" class="frm_input frm_full" placeholder="트래픽,플레이스,찜...">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>상품공급가</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[supplyPrice]" id="supplyPrice" value="" class="frm_input frm_full mask-num">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>상품시장가</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[marketPrice]" id="marketPrice" value="" class="frm_input frm_full mask-num">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>운영일 단위</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[operateUnit]" id="operateUnit" value="" class="frm_input frm_full mask-num">
                    </div>
                    <div class="frm-input wfpx-60 input-append">
                        <span class="frm_text">일</span>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>유입수</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[flowCount]" id="flowCount" value="" class="frm_input frm_full" placeholder="100유입...">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>클릭수 체크</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                <?php
                foreach(['Y' => '예','N' => '아니오'] as $key=>$val) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input type="radio" name="formData[clickCountCheck]" id="clickCountCheck_'.$key.'" value="'.$key.'">';
                        echo '<label for="clickCountCheck_'.$key.'">'.$val.'</label>';
                    echo '</div>';
                }
                ?>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>관리자 UI</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                <?php
                foreach(['Y' => '예','N' => '아니오'] as $key=>$val) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input type="radio" name="formData[existsUi]" id="existsUi_'.$key.'" value="'.$key.'">';
                        echo '<label for="existsUi_'.$key.'">'.$val.'</label>';
                    echo '</div>';
                }
                ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>접수마감 시간</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[closeTime]" id="closeTime" value="" class="frm_input frm_full" placeholder="오후5시...">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>운영시작 시간</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[startTime]" id="startTime" value="" class="frm_input frm_full" placeholder="익일구동...">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>적용가능 쇼핑몰</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[settingItems]" id="settingItems" value="" class="frm_input frm_full" placeholder="자사몰,스마트스토어...">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>운영환경</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[flowAdvice]" id="flowAdvice" value="" class="frm_input frm_full" placeholder="모바일,PC...">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>키워드 설명</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[keywordType]" id="keywordType" value="" class="frm_input frm_full" placeholder="셋팅 시 참조할 키워드 관련 설명을 적어주세요.">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>운영 상태</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                <?php
                foreach(['운영중', '운영중지'] as $key=>$val) {
                    echo '<div class="frm-input frm-check">';
                        echo '<input type="radio" name="formData[status]" id="status_'.$key.'" value="'.$key.'">';
                        echo '<label for="status_'.$key.'">'.$val.'</label>';
                    echo '</div>';
                }
                ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>프로그램 메모</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[memo]" id="memo" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<script>
var programData = <?= json_encode($programData); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(programData, 'formData', fillFormLayout);
});

function fillFormLayout() {
}

App.registerCallback('updateProgramData', function(data) {
    console.log(data);
    if (data.message) {
        alert(data.message);
    }
});
</script>