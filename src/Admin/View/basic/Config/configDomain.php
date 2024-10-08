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
            <button type="button" value="확인" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/config/configDomainUpdate" data-callback="updateConfigDomain">확인</button>
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
                <span>레이아웃 설정</span>
            </div>
            <div class="table-td col-md-10">
                <div class="row">
                    <div class="col-auto">
                        <div class="custom-control custom-radio custom-control-inline layout-box mr-3">
                            <input type="radio" name="formData[cf_layout]" id="cf_layout_1" class="custom-control-input" value="1">
                            <label for="cf_layout_1" class="custom-control-label cursor-pointer">
                                <div class="layout_box">
                                    <div class="mini_content_box mini_content_full"></div>
                                </div>
                                <span>전체 레이아웃</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="custom-control custom-radio custom-control-inline layout-box mr-3">
                            <input type="radio" name="formData[cf_layout]" id="cf_layout_2" class="custom-control-input" value="2">
                            <label for="cf_layout_2" class="custom-control-label cursor-pointer">
                                <div class="layout_box">
                                    <div class="mini_side_box"></div>
                                    <div class="mini_content_box"></div>
                                </div>
                                <span>2단 좌측 레이아웃</span>
                            </label>
                        </div>
                        <div class="input_form_wrap input-group mt-3">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">좌측넓이</span>
                            <input type="text" name="formData[left_width][2]" id="left_width2" class="frm_input frm_small" value="" style="width:40px;min-width:40px;border-radius:0;border-left:0;border-right:0;">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">px</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="custom-control custom-radio custom-control-inline layout-box mr-3">
                            <input type="radio" name="formData[cf_layout]" id="cf_layout_3" class="custom-control-input" value="3">
                            <label for="cf_layout_3" class="custom-control-label cursor-pointer">
                                <div class="layout_box">
                                    <div class="mini_content_box"></div>
                                    <div class="mini_side_box"></div>
                                </div>
                                <span>2단 우측 레이아웃</span>
                            </label>
                        </div>
                        <div class="input_form_wrap input-group mt-3">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">우측넓이</span>
                            <input type="text" name="formData[right_width][3]" id="right_width3" class="frm_input frm_small"" style="width:40px;min-width:40px;border-radius:0;border-left:0;border-right:0;">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">px</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="custom-control custom-radio custom-control-inline layout-box mr-3">
                            <input type="radio" name="formData[cf_layout]" id="cf_layout_4" class="custom-control-input" value="4">
                            <label for="cf_layout_4" class="custom-control-label cursor-pointer">
                                <div class="layout_box">
                                    <div class="mini_side_box"></div>
                                    <div class="mini_content_box mini_content_small"></div>
                                    <div class="mini_side_box"></div>
                                </div>
                                <span>3단 레이아웃</span>
                            </label>
                        </div>
                        <div class="input_form_wrap input-group mt-3">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">좌측넓이</span>
                            <input type="text" name="formData[left_width][4]" id="left_width4" class="frm_input frm_small" value="" style="width:40px;min-width:40px;border-radius:0;border-left:0;border-right:0;">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">px</span>
                        </div>
                        <div class="input_form_wrap input-group mt-1">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">우측넓이</span>
                            <input type="text" name="formData[right_width][4]" id="right_width4" class="frm_input frm_small" value="" style="width:40px;min-width:40px;border-radius:0;border-left:0;border-right:0;">
                            <span style="display:inline-block;padding:.375rem .75rem;background-color:#e9ecef;border:1px solid #ced4da;">px</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>메인화면 전체사용</span>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex">
                    <?php foreach(array('예','아니요') as $key=>$val): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="formData[cf_index_wide]" id="cf_index_wide_<?= $key; ?>" value="<?= $key; ?>">
                        <label class="form-check-label" for="cf_index_wide_<?= $key; ?>"><?= $val; ?></label>
                    </div>
                    <?php endforeach; ?>
                    <span class="form-control bg-info text-white border-0">메인화면 전체사용을 선택하시면 메인화면에는 레이아웃이 적용되지 않습니다.</span>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_layout_max_width" class="form-label">레이아웃 최대넓이</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_layout_max_width]" value="" id="cf_layout_max_width" class="form-control me-2" placeholder="1200" style="max-width: 100px;">
                    <span class="form-control bg-info text-white border-0">레이아웃 최대 넓이입니다. PC에만 적용됩니다.(권장 1,200px)</span>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_content_max_width" class="form-label">내용 최대넓이</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="formData[cf_content_max_width]" value="" id="cf_content_max_width" class="form-control me-2" placeholder="1200" style="max-width: 100px;">
                    <span class="form-control bg-info text-white border-0">내용 최대 넓이입니다. PC에만 적용됩니다.(권장 1,200px)</span>
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
var configDomain = <?php echo json_encode($config_domain); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(configDomain, 'formData', fillFormLayout);
});

function fillFormLayout() {
    console.log(configDomain);
    if (configDomain.cf_layout === 2) {
        document.getElementById('left_width2').value = configDomain.cf_left_width;
    }

    if (configDomain.cf_layout === 3) {
        document.getElementById('right_width3').value = configDomain.cf_right_width;
    }

    if (configDomain.cf_layout === 4) {
        document.getElementById('left_width4').value = configDomain.cf_left_width;
        document.getElementById('right_width4').value = configDomain.cf_right_width;
    }
}

App.registerCallback('updateConfigDomain', function(data) {

});
</script>