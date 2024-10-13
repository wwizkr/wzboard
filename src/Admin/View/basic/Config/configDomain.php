<!-- 탭 네비게이션 -->
<ul class="nav nav-tabs sticky-tabs" id="form-tab" role="tablist">
    <?php foreach ($anchor as $id => $tabs): ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $id === 'anc_cf_basic' ? 'active' : ''; ?>" href="#<?= $id; ?>"><?= $tabs; ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- 폼 컨테이너들 -->
<form name="frm" id="frm">
<input type="hidden" name="cf_id" value="<?= $config_domain['cf_id'];?>">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?= $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <button type="button" value="확인" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/config/configDomainUpdate" data-callback="updateConfigDomain">확인</button>
        </div>
    </div>
</div>
<div class="page-container">
    <h2>홈페이지 정보</h2>
    <div id="anc_cf_basic" class="table-form">
        <div class="table-row">
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
        <div class="table-row">
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
        <div class="table-row">
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
        <div class="table-row">
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
        <div class="table-row">
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
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>레이아웃 설정</span>
            </div>
            <div class="table-td col-md-10">
                <div class="row">
                    <div class="col-auto">
                        <div class="custom-control custom-radio custom-control-inline layout-box">
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
                        <div class="custom-control custom-radio custom-control-inline layout-box">
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
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>메인화면 전체사용</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <?php foreach(array('예','아니요') as $key=>$val): ?>
                    <div class="frm-check">
                        <input type="radio" name="formData[cf_index_wide]" id="cf_index_wide_<?= $key; ?>" value="<?= $key; ?>">
                        <label for="cf_index_wide_<?= $key; ?>"><?= $val; ?></label>
                    </div>
                    <?php endforeach; ?>
                    <span class="frm-guide">메인화면 전체사용을 선택하시면 메인화면에는 레이아웃이 적용되지 않습니다.</span>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <label for="cf_layout_max_width" class="form-label">레이아웃 최대넓이</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_layout_max_width]" value="" id="cf_layout_max_width" class="form-control frm_full" placeholder="1200">
                    </div>
                    <span class="frm-guide">레이아웃 최대 넓이입니다. PC에만 적용됩니다.(권장 1,200px)</span>
                </div>
            </div>
            <div class="table-th col-md-2">
                <label for="cf_content_max_width" class="form-label">내용 최대넓이</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_content_max_width]" value="" id="cf_content_max_width" class="form-control me-2" placeholder="1200">
                    </div>
                    <span class="frm-guide">내용 최대 넓이입니다. PC에만 적용됩니다.(권장 1,200px)</span>
                </div>
            </div>
        </div>
        <?php foreach ($skin as $skinItem): ?>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span><?= htmlspecialchars($skinItem['title']); ?></span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <?php foreach ($skinItem['skin'] as $skinOption): ?>
                        <div class="frm-input frm-check">
                            <input type="radio" name="formData[cf_skin_<?= strtolower($skinItem['name']); ?>]" id="<?= strtolower($skinItem['name']); ?>_<?= htmlspecialchars($skinOption); ?>" value="<?= htmlspecialchars($skinOption); ?>"
                            <?= ($config_domain['cf_skin_' . strtolower($skinItem['name'])] == $skinOption) ? 'checked' : ''; ?>>
                            <label for="<?= strtolower($skinItem['name']); ?>_<?= htmlspecialchars($skinOption); ?>">
                                <?= htmlspecialchars($skinOption); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <span class="frm-desc"><?= htmlspecialchars($skinItem['desc']); ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>위젯 사용 설정</span>
            </div>
            <div class="table-td col-md-10">
            <?php foreach($widget as $key=>$dir) { ?>
                <div class="frm-input-row">
                    <div class="frm-input frm-check wfpx-150">
                        <input type="checkbox" name="formData[cf_<?= $dir['field']; ?>]" value="1" id="cf_<?= $dir['field']; ?>">
                        <label for="cf_<?= $dir['field']; ?>"><?= $dir['title']; ?> 사용</label>
                    </div>
                    <div class="frm-desc frm-ml">사용 스킨 선택</div>
                    <?php foreach($dir['skin'] as $index => $widgetSkin) { ?>
                    <div class="frm-check">
                        <input type="radio" name="formData[cf_<?= $dir['field']; ?>_skin]" id="cf_<?= $dir['field']; ?>_skin_<?= $index; ?>" value="<?= $widgetSkin; ?>">
                        <label for="cf_<?= $dir['field']; ?>_skin_<?= $index; ?>"><?= $widgetSkin; ?></label>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>파비콘 설정</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">파비콘을 직접 올릴 수 있습니다. ICO 파일만 가능합니다.</span>
                <div class="frm-input-row file-wrap">
                    <div class="frm-input frm-file">
                        <input type="file" name="fileData[ico_img]" id="ico_img" accept=".ico">
                        <label for="ico_img">파일 등록</label>
                    </div>
                    <div class="frm-input file-name"></div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="ico_img_del" value="1" id="ico_img_del">
                        <label for="ico_img_del">삭제</label>
                    </div>
                    <div class="frm-input wf-ml-auto">
                        <img src="" class="image-preivew" style="max-height:50px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>로고 이미지(PC용)</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">로고를 등록합니다. PC에 사용되는 로고입니다. png 투명파일로 등록하시면 좋습니다.</span>
                <div class="frm-input-row file-wrap">
                    <div class="frm-input frm-file">
                        <input type="file" name="fileData[pc_logo_image]" id="pc_logo_image" accept="image/*">
                        <label for="pc_logo_image">파일 등록</label>
                    </div>
                    <div class="frm-input file-name"></div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="pc_logo_image_del" value="1" id="pc_logo_image_del">
                        <label for="pc_logo_image_del">삭제</label>
                    </div>
                    <div class="frm-input wf-ml-auto">
                        <img src="" class="image-preivew" style="max-height:50px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>로고 이미지(모바일용)</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">로고를 등록합니다. 모바일에 사용되는 로고입니다. png 투명파일로 등록하시면 좋습니다.</span>
                <div class="frm-input-row file-wrap">
                    <div class="frm-input frm-file">
                        <input type="file" name="fileData[mo_logo_image]" id="mo_logo_image" accept="image/*">
                        <label for="mo_logo_image">파일 등록</label>
                    </div>
                    <div class="frm-input file-name"></div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="mo_logo_image_del" value="1" id="mo_logo_image_del">
                        <label for="mo_logo_image_del">삭제</label>
                    </div>
                    <div class="frm-input wf-ml-auto">
                        <img src="" class="image-preivew" style="max-height:50px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>오픈그래프용 이미지</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">오픈그래프용 이미지를 직접 올릴 수 있습니다. JPG 파일만 가능합니다. <b>4:3 비율</b>의 이미지가 적당합니다.</span>
                <div class="frm-input-row file-wrap">
                    <div class="frm-input frm-file">
                        <input type="file" name="fileData[og_image]" id="og_image" accept=".jpg">
                        <label for="og_image">파일 등록</label>
                    </div>
                    <div class="frm-input file-name"></div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="og_image_del" value="1" id="og_image_del">
                        <label for="og_image_del">삭제</label>
                    </div>
                    <div class="frm-input wf-ml-auto">
                        <img src="" class="image-preivew" style="max-height:50px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2>회원 설정</h2>
    <div id="anc_cf_member" class="table-form">
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>회원가입 승인</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_auto_register]" value="1" id="cf_auto_register">
                        <label for="cf_auto_register">관리자 승인</label>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>이메일 인증</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_email_certify]" value="1" id="cf_use_email_certify">
                        <label for="cf_use_email_certify">이메일 인증 사용</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>회원가입시 레벨</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160"><?= $memberSelect; ?></div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>회원 자동 등업</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_auto_levelup]" value="1" id="cf_auto_levelup">
                        <label for="cf_auto_levelup">자동등업 사용</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>휴대폰번호 입력</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_hp]" value="1" id="cf_use_hp">
                        <label for="cf_use_hp">보이기</label>
                    </div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_req_hp]" value="1" id="cf_req_hp">
                        <label for="cf_req_hp">필수입력</label>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>주소 입력</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_addr]" value="1" id="cf_use_addr">
                        <label for="cf_use_addr">보이기</label>
                    </div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_req_addr]" value="1" id="cf_req_addr">
                        <label for="cf_req_addr">필수입력</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>추천인</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_recommend]" value="1" id="cf_use_recommend">
                        <label for="cf_use_recommend">추천인 사용</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2>SNS 연결 설정</h2>
    <div id="anc_cf_sns" class="table-form">
        <div class="table-row">
        <?php foreach($snsLogin as $key=>$sns) { ?>
            <div class="table-th col-md-2">
                <span><?= $sns['title']; ?></span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_social_servicelist][]" value="<?= $key; ?>" id="cf_use_social_<?= $key; ?>">
                        <label for="cf_use_social_<?= $key; ?>">사용</label>
                    </div>
                    <div class="frm-guide"><a href="<?= $sns['guideUrl']; ?>" target="_blank">로그인 가이드</a></div>
                    <div class="frm-guide">Callback URL : <?= $sns['callBackUrl']; ?></div>
                </div>
                <?php foreach($sns['usefield'] as $field=>$title) { ?>
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160 input-prepend">
                        <span class="frm_text"><?= $title; ?></span>
                    </div>
                    <div class="frm-input wfpe-50">
                        <input type="text" name="formData[cf_<?= $field; ?>]" value="" id="cf_<?= $field; ?>" class="frm_input frm_full">
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    </div>

    <h2>적립금 설정</h2>
    <div id="anc_cf_point" class="table-form">
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>적립금</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_point]" value="1" id="cf_use_point">
                        <label for="cf_use_point">적립금 사용</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>회원가입 적립금</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">회원가입 시 최초 1회 지급되는 적립금</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_join_point]" id="cf_join_point" value="">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>로그인 적립금</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">로그인 시 일일 1회 지급되는 적립금</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_login_point]" id="cf_login_point" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>추천인 적립금(회원가입)</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">회원가입 시 추천인에게 최초 1회 지급되는 적립금</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_recommend_member_point]" id="cf_recommend_member_point" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>게시판 적립금(글읽기)</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">게시판 글읽기 적립금입니다. -를 입력할 경우 차감됩니다.</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_board_read_point]" id="cf_board_read_point" value="">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>게시판 적립금(글쓰기)</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">게시판 글쓰기 적립금입니다. -를 입력할 경우 차감됩니다.</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_board_write_point]" id="cf_board_write_point" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>게시판 적립금(댓글쓰기)</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">게시판 댓글쓰기 적립금입니다. -를 입력할 경우 차감됩니다.</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_board_comment_point]" id="cf_board_comment_point" value="">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>게시판 적립금(다운로드)</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">게시판 파일 다운로드 적립금입니다. -를 입력할 경우 차감됩니다.</span>
                <div class="frm-input-row">
                    <div class="frm-input">
                        <input type="text" class="frm_input mask-num" name="formData[cf_board_download_point]" id="cf_board_download_point" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2>SEO/스크립트 설정</h2>
    <div id="anc_cf_seo" class="table-form">
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>홈페이지 키워드</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" class="frm_input frm_full" name="formData[cf_seo_keyword]" id="cf_seo_keyword" value="" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>홈페이지 설명</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <input type="text" class="frm_input frm_full" name="formData[cf_seo_description]" id="cf_seo_description" value="" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>방문자 분석 스크립트</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">방문자 분석 스크립트 코드를 입력합니다. 엔터로 구분하여 한 줄씩 입력하세요. 예) 구글 애널리틱스</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_analytics]" id="cf_analytics" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>추가 메타태그</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">추가로 사용하실 meta 태그를 입력합니다.</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_add_meta]" id="cf_add_meta" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>네이버 검색광고</span>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="formData[cf_use_naver_ad]" id="cf_use_naver_ad" value="Y">
                        <label for="cf_use_naver_ad">전환추적 코드 사용</label>
                    </div>
                    <div class="frm-guide">
                        <span>사용에 체크하시면 네이버 검색광고를 사용중이실 경우, 추적코드를 사용합니다.</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input wfpx-190 input-prepend">
                        <span class="frm_text">네이버 검색광고 네이버공통키</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[cf_use_naver_ad_key]" id="cf_use_naver_ad_key" value="" class="frm_input frm_full">
                     </div>
                     <div class="frm-guide">
                        <span>네이버 검색광고 프리미엄 로그분석 서비스의 네이버 공통키를 입력해 주세요. 네이버검색광고 -> 도구 -> 서비스사용현황</span>
                     </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input wfpx-190 input-prepend">
                        <span class="frm_text">네이버 검색광고 전환추적 선택</span>
                    </div>
                    <?php
                    foreach(array('join'=>'회원가입','order'=>'주문접수','call'=>'상담신청') as $key=>$val) {
                        echo '<div class="frm-input frm-check">';
                            echo '<input type="checkbox" name="formData[cf_use_naver_ad_type][]" id="cf_use_naver_ad_type_'.$key.'" value="'.$key.'">';
                            echo '<label for="cf_use_naver_ad_type_'.$key.'">'.$val.'</label>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>SNS 연관채널</span>
            </div>
            <div class="table-td col-md-10">
                <span class="helper">네이버 검색에 사용되는 SNS 연관채널입니다. 운영하시는 SNS가 있으시다면 URL을 등록해 주세요.</span>
                <?php
                $snsUrl = $config_domain['cf_sns_channel_url'] ? unserialize($config_domain['cf_sns_channel_url']) : [];
                foreach($snsSeo as $key=>$val) {
                    $value = explode(",",$val);
                    $sns_name = $value[0];
                    $sns_ex  = $value[1];
                    $sns_url = $snsUrl[$key] ?? '';
                    echo '<div class="frm-input-row">';
                        echo '<div class="frm-input input-prepend wfpx-160">';
                            echo '<span class="frm_text">'.$sns_name.'</span>';
                        echo '</div>';
                        echo '<div class="frm-input input-auto">';
                            echo '<input type="text" name="formData[cf_sns]['.$key.']" id="cf_sns_'.$key.'" value="'.$sns_url.'" class="frm_input frm_full" placeholder="'.$sns_ex.'">';
                        echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <h2>기타 설정</h2>
    <div id="anc_cf_etc" class="table-form">
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>페이지당 목록수(PC)</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_pc_page_rows]" id="cf_pc_page_rows" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>페이지당 목록수(모바일)</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_mo_page_rows]" id="cf_mo_page_rows" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>페이지당 페이지수(PC)</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_pc_page_nums]" id="cf_pc_page_nums" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>페이지당 페이지수(모바일)</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_mo_page_nums]" id="cf_mo_page_nums" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>현재 접속자</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">설정값 이내의 접속자를 현재 접속자로 인정</span>
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_login_minutes]" id="cf_login_minutes" class="frm_input frm_full">
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>접속자 로그 삭제</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">설정값 이내의 접속자를 현재 접속자로 인정</span>
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <input type="text" name="formData[cf_visit_del]" id="cf_visit_del" class="frm_input frm_full">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>관리자 접속 IP</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">입력된 IP의 컴퓨터만 관리자 페이지에 접속할 수 있습니다. 123.123.+ 도 입력 가능. (엔터로 구분)</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_allow_admin_ip]" id="cf_allow_admin_ip" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>접근 차단 IP</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">입력된 IP의 컴퓨터는 접근할 수 없음. 123.123.+ 도 입력 가능. (엔터로 구분)</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_intercept_ip]" id="cf_intercept_ip" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row">
            <div class="table-th col-md-2">
                <span>접근 가능 IP</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">입력된 IP의 컴퓨터만 접근할 수 있습니다. 123.123.+ 도 입력 가능. (엔터로 구분)</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_possible_ip]" id="cf_possible_ip" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>아이디 금지단어</span>
            </div>
            <div class="table-td col-md-4">
                <span class="helper">회원아이디로 사용할 수 없는 단어를 정합니다. 쉼표 (,) 로 구분</span>
                <div class="frm-input-row">
                    <div class="frm-input frm-input-full">
                        <textarea name="formData[cf_prohibit_id]" id="cf_prohibit_id" class="frm_input frm_full"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
var configDomain = <?= json_encode($config_domain); ?>;
var images = <?= json_encode($image); ?>;
document.addEventListener('DOMContentLoaded', function() {
    fillFormData(configDomain, 'formData', fillFormLayout);
});

function fillFormLayout() {
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
    if (data.message) {
        alert(data.message);
    }
});
</script>