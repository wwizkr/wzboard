<!-- 탭 네비게이션 -->
<ul class="nav nav-tabs sticky-tabs" id="form-tab" role="tablist">
    <?php foreach ($anchor as $id => $tabs): ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $id === 'anc_cf_basic' ? 'active' : ''; ?>" href="#<?php echo $id; ?>"><?php echo $tabs; ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- 폼 컨테이너들 -->
<form name="frm" id="frm">
<input type="hidden" name="cf_id" value="<?php echo $config_domain['cf_id'];?>">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <button type="button" value="확인" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/settings/update">확인</button>
        </div>
    </div>
</div>
<div class="page-container">
    <h2>홈페이지 정보</h2>
    <div id="anc_cf_basic" class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_title" class="form-label">홈페이지 제목</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="formData[cf_title]" value="" id="cf_title" class="form-control" placeholder="홈페이지 제목">
            </div>
            <div class="table-th col-md-2">
                <label for="cf_domain" class="form-label">홈페이지 URL</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="formData[cf_domain]" value="" id="cf_domain" class="form-control" placeholder="홈페이지 주소">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_name" class="form-label">회사명</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="formData[cf_company_name]" value="" id="cf_company_name" class="form-control" placeholder="회사명">
            </div>
            <div class="table-th col-md-2">
                <label for="cf_company_owner" class="form-label">대표자명</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="formData[cf_company_owner]" value="" id="cf_company_owner" class="form-control" placeholder="대표자명">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_number_1" class="form-label">사업자등록번호</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_company_number][]" id="cf_company_number_1" class="form-control me-1" placeholder="000" maxlength="3" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="formData[cf_company_number][]" id="cf_company_number_2" class="form-control mx-1" placeholder="00" maxlength="2" style="max-width: 50px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="formData[cf_company_number][]" id="cf_company_number_3" class="form-control ms-1" placeholder="00000" maxlength="5" style="max-width: 100px;">
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_tongsin_number" class="form-label">통신판매업번호</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_tongsin_number]" value="" id="cf_tongsin_number" class="form-control" placeholder="통신판매업번호">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_tel_1" class="form-label">대표 전화번호</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_company_tel][]" id="cf_company_tel_1" class="form-control me-1" placeholder="000" maxlength="3" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="formData[cf_company_tel][]" id="cf_company_tel_2" class="form-control mx-1" placeholder="0000" maxlength="4" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="formData[cf_company_tel][]" id="cf_company_tel_3" class="form-control ms-1" placeholder="0000" maxlength="4" style="max-width: 80px;">
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_company_email" class="form-label">대표 이메일</label>
            </div>
            <div class="table-td col-md-4">
                <input type="email" name="formData[cf_company_email]" id="cf_company_email" class="form-control" placeholder="example@example.com">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_zip" class="form-label">주소</label>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex align-items-center mb-2">
                    <input type="text" name="formData[cf_company_zip]" id="cf_company_zip" class="form-control me-2" placeholder="우편번호" maxlength="5" style="max-width: 100px;">
                    <button type="button" class="btn btn-primary">우편번호 찾기</button>
                </div>
                <div class="mb-2">
                    <input type="text" name="formData[cf_company_addr1]" id="cf_company_addr1" class="form-control" placeholder="주소 1">
                </div>
                <div class="mb-2">
                    <input type="text" name="formData[cf_company_addr2]" id="cf_company_addr2" class="form-control" placeholder="주소 2 (상세 주소)">
                </div>
                <div>
                    <input type="text" name="formData[cf_company_addr3]" id="cf_company_addr3" class="form-control" placeholder="주소 3 (참고 항목)">
                </div>
            </div>
        </div>
    </div>

    <h2>레이아웃 설정</h2>
    <div id="anc_cf_layout" class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_max_width" class="form-label">홈페이지 최대넓이</label>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_max_width]" value="" id="cf_max_width" class="form-control me-2" placeholder="1200" style="max-width: 100px;">
                    <span class="form-control bg-info text-white border-0">홈페이지 최대 넓이입니다. PC에만 적용됩니다.(권장 1,200px)</span>
                </div>
            </div>
        </div>
        <?php foreach ($skin as $skinItem): ?>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label class="form-label"><?php echo htmlspecialchars($skinItem['title']); ?></label>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex flex-wrap">
                    <?php foreach ($skinItem['skin'] as $skinOption): ?>
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="formData[cf_skin_<?php echo strtolower($skinItem['name']); ?>]" id="<?php echo strtolower($skinItem['name']); ?>_<?php echo htmlspecialchars($skinOption); ?>" value="<?php echo htmlspecialchars($skinOption); ?>"
                            <?php echo ($config_domain['cf_skin_' . strtolower($skinItem['name'])] == $skinOption) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="<?php echo strtolower($skinItem['name']); ?>_<?php echo htmlspecialchars($skinOption); ?>">
                                <?php echo htmlspecialchars($skinOption); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <span class="form-text"><?php echo htmlspecialchars($skinItem['desc']); ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h2>회원 설정</h2>
    <div id="anc_cf_member" class="table-form">
        
        
    </div>

    <h2>SEO/스크립트 설정</h2>
    <div id="anc_cf_seo" class="table-form">
        
        
    </div>

    <h2>기타 설정</h2>
    <div id="anc_cf_etc" class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_naver_clientid" class="form-label">네이버 클라이언트 ID</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_naver_clientid]" value="" id="cf_naver_clientid" class="form-control me-2" placeholder="Naver Client ID" style="max-width: 260px;">
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_naver_secret" class="form-label">네이버 Api Key</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_naver_secret]" value="" id="cf_naver_secret" class="form-control me-2" placeholder="Naver Client Secret" style="max-width: 260px;">
                </div>
            </div>
        </div>
        
    </div>
</div>
</form>
<script>
var settingData = <?php echo json_encode($config_domain); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(settingData, 'formData');
});

function frm_submit(f) {
    
}
</script>