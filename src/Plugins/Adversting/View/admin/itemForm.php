<div class="page-container form-container">
    <form name="frm" id="frm">
    <input type="hidden" name="itemNo" value="<?= $itemData['no'] ?? 0; ?>">
    <input type="hidden" name="period" id="period" value="">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?= $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="/adversting/admin/itemList?<?= $queryString; ?>" class="btn btn-fill-darkgray">목록</a>
                <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/adversting/admin/itemUpdate" data-callback="updateItemData">확인</button>
            </div>
        </div>
    </div>
    <div class="table-form">
        <h2 class="form-title">광고 상품 정보</h2>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>판매자(셀러) 입력</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[sellerId]" id="sellerId" value="" class="frm_input frm_full confirm" <?= !empty($itemData) ? 'readonly' : ''; ?>>
                    </div>
                    <?php if (empty($itemData)) { ?>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-fill-colorgreen" id="checkSellerId" onclick="validateSellerId(this);">아이디확인</button>
                    </div>
                    <div class="frm-input frm-ml validate-message"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>프로그램 선택</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <select name="formData[programNo]" id="programNo" class="frm_input frm_full">
                            <option value="">프로그램 선택</option>
                            <?php
                            foreach($programList as $key => $val) {
                                echo '<option value="'.$val['no'].'">'.$val['companyName'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>프로그램 분류</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row" id="program-type"></div>
            </div>
            <div class="table-th col-md-2">
                <span>프로그램 상품종류</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row" id="program-item"></div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>쇼핑몰 업체 선택</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row"">
                    <div class="frm-input wfpx-160">
                        <select name="formData[storeType]" id="storeType" class="frm_input frm_full">
                            <option value="">쇼핑몰 업체 선택</option>
                            <?php
                            foreach($storeConfig['storeType'] as $key => $val) {
                                echo '<option value="'.$key.'">'.$val.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>쇼핑몰 분류</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row"">
                    <div class="frm-input wfpx-160">
                        <select name="formData[storeKind]" id="storeKind" class="frm_input frm_full">
                            <option value="">쇼핑몰 분류 선택</option>
                            <?php
                            foreach($storeConfig['storeKind'] as $key => $val) {
                                echo '<option value="'.$key.'">'.$val.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>쇼핑몰명</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[storeName]" id="storeName" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>상품 URL</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" name="formData[itemUrl]" id="itemUrl" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>상품명</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpe-50">
                        <input type="text" name="formData[itemName]" id="itemName" value="" class="frm_input frm_full">
                    </div>
                    <div class="frm-guide">
                        <span>네이버 등 쇼핑몰에 등록된 실제 상품명을 입력하세요</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>상품고유번호(MID)</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[itemCode]" id="itemCode" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>가격비교 원부코드</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[matchCode]" id="matchCode" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>검색어</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input input-prepend">
                        <span class="frm_text">기본 검색어</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[searchKeyword]" id="searchKeyword" value="" class="frm_input frm_full">
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">이전 검색어</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[oQuery]" id="oQuery" value="" class="frm_input frm_full">
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">광고매체 검색어</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[adQuery]" id="adQuery" value="" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>슬롯 설정</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input input-prepend">
                        <span class="frm_text">슬롯수량</span>
                    </div>
                    <div class="frm-input wfpx-60">
                        <input type="text" name="formData[slotCount]" id="slotCount" value="" class="frm_input frm_full text-right">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">개</span>
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">운영기간</span>
                    </div>
                    <div class="frm-input wfpx-60">
                        <input type="text" name="formData[slotPeriod]" id="slotPeriod" value="" class="frm_input frm_full text-right">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">일</span>
                    </div>
                    <?php if (!empty($itemData['no'])) { ?>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-fill-colorpurple" id="extensionButton" onclick="calculateOperationDate();">기간연장</button>
                    </div>
                    <div class="frm-guide">
                        <span>기간을 연장하기 위해서는 반드시 기간연장 버튼을 눌러주세요</span>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>슬롯 운영일</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input input-prepend">
                        <span class="frm_text">시작일</span>
                    </div>
                    <div class="frm-input wfpx-120">
                        <input type="text" name="formData[start_at]" id="start_at" value="" class="frm_input frm_full <?= !empty($itemData) ? '' : 'datepicker'; ?>" <?= !empty($itemData) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">연장일</span>
                    </div>
                    <div class="frm-input wfpx-120">
                        <input type="text" name="formData[extension_at]" id="extension_at" value="" class="frm_input frm_full" readonly>
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">종료일</span>
                    </div>
                    <div class="frm-input wfpx-120">
                        <input type="text" name="formData[close_at]" id="close_at" value="" class="frm_input frm_full" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>순위 확인</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input input-prepend">
                        <span class="frm_text">등록 시 순위</span>
                    </div>
                    <div class="frm-input wfpx-60">
                        <input type="text" name="formData[startRanking]" id="startRanking" value="" class="frm_input frm_full" <?= !empty($itemData) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="frm-input frm-ml input-prepend">
                        <span class="frm_text">현재 순위</span>
                    </div>
                    <div class="frm-input wfpx-60">
                        <input type="text" name="formData[updateRanking]" id="updateRanking" value="" class="frm_input frm_full">
                    </div>
                    <div class="frm-input frm-ml">
                        <button type="button" class="btn btn-fill-colorgreen" id="checkRanking" onclick="processedCheckRanking();">순위확인</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<script>
var itemData = <?= json_encode($itemData); ?>;
var programList = <?= json_encode($programList); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(itemData, 'formData', fillFormLayout);

    // jQuery가 로드되었는지 확인 후 컬러 피커와 데이트피커 초기화
    if (typeof jQuery !== 'undefined') {
        if (jQuery.fn.datepicker) {
            initializeDatepicker(startOperationDate); //
        } else {
            console.error('jQuery UI Datepicker is not loaded');
        }
    } else {
        console.error('jQuery is not loaded');
    }

    const selectProgram = document.getElementById('programNo');
    selectProgram.addEventListener('change', function () {
        let selectedProgram = selectProgram.value;
        createProgramSelect(selectedProgram);
    });

    // 강제로 change 이벤트 실행
    selectProgram.dispatchEvent(new Event('change'));
});

function fillFormLayout() {
    if (typeof itemData.no !== 'undefined') {
        const startAtInput = document.getElementById('start_at');
        const extensionAtInput = document.getElementById('extension_at');
        const closeAtInput = document.getElementById('close_at');
        startAtInput.value = startAtInput.value.slice(0, 10);
        extensionAtInput.value = extensionAtInput.value.slice(0, 10);
        closeAtInput.value = closeAtInput.value.slice(0, 10);
    }
}

function validateSellerId(el) {
    const sellerIdInput = document.getElementById('sellerId');
    const sellerId = sellerIdInput.value;
    
    if (!sellerId) {
        alert('판매자 아이디를 입력해 주세요');
        return false;
    }

    var data = {sellerId: sellerId};
    var url = '/adversting/admin/checkSellerId';

    sendCustomAjaxRequest('POST', url, data, false)
        .then(response => {
            validateSuccessSellerId(response, el); //el 사용
        })
        .catch(error => {
            if (error.response) {
                console.error('에러 응답:', error.response);
            }
            alert('아이디 검증에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
        });
}

function validateSuccessSellerId(data, el) {
    if (data.result === 'failure') {
        alert(data.message);
        document.getElementById('sellerId').value = '';
        return false;
    }
    let html = '<span style="color:var(--colorblue);">상품 등록이 가능한 아이디 입니다.</span>';
    
    const parentRow = el.closest('.frm-input-row');
    if (parentRow) {
        const messageElement = parentRow.querySelector('.validate-message');
        if (messageElement) {
            messageElement.innerHTML = html;
        }
    }
}

function createProgramSelect(programNo) {
    const programData = programList[programNo];
    
    if (programNo && typeof programData === 'undefined') {
        alert('프로그램 정보를 찾을 수 없습니다.');
        return false;
    }
    
    let html1 = `
        <div class="frm-input wfpx-160">
            <input type="text" name="formData[programType]" id="progranType" value="${programData.programType}" class="frm_input frm_full">
        </div>
    `;
    // 생성된 HTML을 원하는 위치에 삽입
    document.getElementById('program-type').innerHTML = html1;

    const items = programData.programItems.split(',');
    let html2 = '';
    items.forEach((item, index) => {
        const checked = item === itemData.programItem ? 'checked' : '';
        html2 += `
            <div class="frm-input frm-check">
                <input type="radio" name="formData[programItem]" id="program_item_${index}" value="${item}" ${checked}>
                <label for="program_item_${index}">${item}</label>
            </div>
        `;
    });
    // 생성된 HTML을 원하는 위치에 삽입
    document.getElementById('program-item').innerHTML = html2;
}

function startOperationDate(date) {
    const operationPeriod = document.getElementById('slotPeriod').value;
    if (!operationPeriod) {
        alert('운영기간을 먼저 입력하세요');
        document.getElementById('start_at').value = '';
        return false;
    }

    // 시작 날짜 문자열을 Date 객체로 변환
    const startDate = new Date(date);
    const periodDays = parseInt(operationPeriod, 10);

    // 운영 기간(일)을 더한 종료일 계산
    startDate.setDate(startDate.getDate() + periodDays);

    // 종료일을 yyyy-mm-dd 형식으로 포맷
    const endDate = formatDateYYYY_MM_DD(startDate);

    // 연장일과 종료일 필드에 값 설정
    document.getElementById('extension_at').value = date;
    document.getElementById('close_at').value = endDate;
}

function calculateOperationDate() {
    const extendStatus = document.getElementById('period').value;

    // 종료일과 운영 기간 입력 필드 가져오기
    const extensionAtInput = document.getElementById('extension_at');
    const closeAtInput = document.getElementById('close_at');
    const slotPeriodInput = document.getElementById('slotPeriod');

    if (extendStatus === 'extend') {
        cf = confirm('이미 기간 연장 상태입니다. 초기화 후 다시 연장하시겠습니까?');
        if (cf === false) {
            return false;
        } else {
            extensionAtInput.value = itemData.extension_at.slice(0, 10);
            closeAtInput.value = itemData.close_at.slice(0, 10);
        }
    }

    // 연장일, 종료일, 운영기간 가져오기
    const extensionAt = extensionAtInput.value;
    const closeAt = closeAtInput.value;
    const slotPeriod = parseInt(slotPeriodInput.value, 10);

    // 종료일과 운영 기간이 올바르게 설정되어 있는지 확인
    if (!closeAt || isNaN(slotPeriod)) {
        alert("종료일과 운영 기간을 입력해 주세요.");
        return;
    }

    // 종료일을 Date 객체로 변환 후 1일 추가
    const closeDate = new Date(closeAt);
    closeDate.setDate(closeDate.getDate() + 1);

    // 연장일 설정
    const extendedDate = formatDateYYYY_MM_DD(closeDate);
    extensionAtInput.value = extendedDate;

    // 연장일에 운영 기간만큼 일수를 더하여 새로운 종료일 계산
    closeDate.setDate(closeDate.getDate() + slotPeriod);
    const newCloseDate = formatDateYYYY_MM_DD(closeDate);
    closeAtInput.value = newCloseDate;

    // 연장상태로 변경
    document.getElementById('period').value = 'extend';
}

async function processedCheckRanking() {
    const storeType = document.getElementById('storeType').value;
    const storeName = document.getElementById('storeName').value;
    const itemCode = document.getElementById('itemCode').value;
    const matchCode = document.getElementById('matchCode').value;
    const searchKeyword = document.getElementById('searchKeyword').value;
    const oQuery = document.getElementById('oQuery').value;
    const adQuery = document.getElementById('adQuery').value;

    if (storeType !== 'navershop') {
        alert('현재 네이버 쇼핑 이외의 쇼핑몰은 순위 검색을 지원하지 않습니다.');
        return false;
    }

    if (!storeName) {
        alert('쇼핑몰명은 필수 입니다.');
        return false;
    }

    if (!itemCode) {
        alert('상품코드는 필수 입니다.');
        return false;
    }

    if (!searchKeyword) {
        alert('기본 검색어는 필수 입니다.');
        return false;
    }

    if (!matchCode) {
        let cf = confirm('가격비교 원부코드가 입력되지 않았습니다. 이대로 검색하시겠습니까?');
        if (cf === false) {
            return false;
        }
    }

    var data = {storeName: storeName, itemCode: itemCode, matchCode: matchCode, searchKeyword: searchKeyword, oQuery: oQuery, adQuery: adQuery};
    var url = '/adversting/admin/searchNaverShopRank';

    try {
       const result = await sendCustomAjaxRequest('POST', url, data, true, 'searchSuccessRanking');
    }
    catch (error) {
        alert('순위 검색에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
    }
}

function searchSuccessRanking(data) {
    if (data.result === 'failure') {
        alert(data.message);
        return false;
    }

    console.log(data);

    document.getElementById('updateRanking').value = data.data.rank;
    
    if (typeof itemData.no === 'undefined') {
        document.getElementById('startRanking').value = data.data.rank;
    }
    
}

App.registerCallback('updateItemData', function(data) {
    console.log(data);
    if (data.message) {
        alert(data.message);
    }
    
    if (typeof itemData.no !== 'undefined') {
        location.reload();
    } else {
        location.href = '/adversting/admin/itemForm/' + data.data.itemNo;
    }
});
</script>