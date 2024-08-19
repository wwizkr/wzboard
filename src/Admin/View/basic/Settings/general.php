<!-- 탭 네비게이션 -->
<ul class="nav nav-tabs sticky-tabs" id="form-tab" role="tablist">
    <?php foreach ($anchor as $id => $title): ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $id === 'anc_cf_basic' ? 'active' : ''; ?>" href="#<?php echo $id; ?>"><?php echo $title; ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- 폼 컨테이너들 -->
<form>
<div class="table-container">
    <h2>홈페이지 정보</h2>
    <div id="anc_cf_basic" class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_title" class="form-label">홈페이지 제목</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_title" value="<?php echo $config_domain['cf_title'];?>" id="cf_title" class="form-control" placeholder="홈페이지 제목">
            </div>
            <div class="table-th col-md-2">
                <label for="cf_domain" class="form-label">홈페이지 URL</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_domain" value="<?php echo $config_domain['cf_domain'];?>" id="cf_domain" class="form-control" placeholder="홈페이지 주소">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_name" class="form-label">회사명</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_company_name" value="<?php echo $config_domain['cf_company_name'];?>" id="cf_company_name" class="form-control" placeholder="회사명">
            </div>
            <div class="table-th col-md-2">
                <label for="cf_company_owner" class="form-label">대표자명</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_company_owner" value="<?php echo $config_domain['cf_company_owner'];?>" id="cf_company_owner" class="form-control" placeholder="대표자명">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_number1" class="form-label">사업자등록번호</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="cf_company_number_1" id="cf_company_number_1" class="form-control me-1" placeholder="000" maxlength="3" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="cf_company_number_2" id="cf_company_number_2" class="form-control mx-1" placeholder="00" maxlength="2" style="max-width: 50px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="cf_company_number_3" id="cf_company_number_3" class="form-control ms-1" placeholder="00000" maxlength="5" style="max-width: 100px;">
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_tongsin_number" class="form-label">통신판매업번호</label>
            </div>
            <div class="table-td col-md-4">
                <input type="text" name="cf_tongsin_number" value="<?php echo $config_domain['cf_tongsin_number'];?>" id="cf_tongsin_number" class="form-control" placeholder="통신판매업번호">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_tel_1" class="form-label">대표 전화번호</label>
            </div>
            <div class="table-td col-md-4">
                <div class="d-flex align-items-center">
                    <input type="text" name="cf_company_tel_1" id="cf_company_tel_1" class="form-control me-1" placeholder="000" maxlength="3" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="cf_company_tel_2" id="cf_company_tel_2" class="form-control mx-1" placeholder="0000" maxlength="4" style="max-width: 80px;">
                    <span class="mx-1">-</span>
                    <input type="text" name="cf_company_tel_3" id="cf_company_tel_3" class="form-control ms-1" placeholder="0000" maxlength="4" style="max-width: 80px;">
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_company_email" class="form-label">대표 이메일</label>
            </div>
            <div class="table-td col-md-4">
                <input type="email" name="cf_company_email" id="cf_company_email" class="form-control" placeholder="example@example.com">
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <label for="cf_company_zip" class="form-label">주소</label>
            </div>
            <div class="table-td col-md-10">
                <div class="d-flex align-items-center mb-2">
                    <input type="text" name="cf_company_zip" id="cf_company_zip" class="form-control me-2" placeholder="우편번호" maxlength="5" style="max-width: 100px;">
                    <button type="button" class="btn btn-primary">우편번호 찾기</button>
                </div>
                <div class="mb-2">
                    <input type="text" name="cf_company_addr1" id="cf_company_addr1" class="form-control" placeholder="주소 1">
                </div>
                <div class="mb-2">
                    <input type="text" name="cf_company_addr2" id="cf_company_addr2" class="form-control" placeholder="주소 2 (상세 주소)">
                </div>
                <div>
                    <input type="text" name="cf_company_addr3" id="cf_company_addr3" class="form-control" placeholder="주소 3 (참고 항목)">
                </div>
            </div>
        </div>
    </div>

    <h2>레이아웃 설정</h2>
    <div id="anc_cf_layout" class="table-form">
        
        
    </div>

    <h2>회원 설정</h2>
    <div id="anc_cf_member" class="table-form">
        
        
    </div>

    <h2>SEO/스크립트 설정</h2>
    <div id="anc_cf_seo" class="table-form">
        
        
    </div>

    <h2>기타 설정</h2>
    <div id="anc_cf_etc" class="table-form">
        
        
    </div>
</div>
</form>