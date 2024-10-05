<form name="frm" id="frm" action="/admin/template/templateUpdate" onsubmit="return frm_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="table" id="table" value="<?= $table; ?>">
<input type="hidden" name="ct_id" id="ct_id" value="<?= $templateData['ct_id']; ?>">
<input type="hidden" name="cg_id" id="cg_id" value="<?= $templateData['cg_id'] ?? 0; ?>">

<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?= $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/template/templateList" class="btn btn-fill-darkgray">템플릿 목록</a>
            <button type="submit" class="btn btn-fill-accent">확인</button>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>출력순서</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-90"><input type="text" name="ct_order" class="frm_input frm_full" id="ct_order" value="<?= $templateData['ct_order']; ?>"></div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>사용</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-90">
                        <select name="ct_use" id="ct_use" class="frm_input frm_full">
                            <option value="0" <?= $templateData['ct_use'] === 0 ? 'selected' : ''; ?>>사용</option>
                            <option value="1" <?= $templateData['ct_use'] === 1 ? 'selected' : ''; ?>>사용안함</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>ID</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_section_id" id="ct_section_id" value="<?= $templateData['ct_section_id']; ?>" class="frm_input frm_full">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">ID는 중복될 수 없습니다.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3 list-config">
            <div class="table-th col-md-2">
                <span>리스트 설정</label>
            </div>
            <div class="table-td col-md-10">
                <!--- 리스트 설정 시작 --->
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">출력위치</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <select name="ct_position" id="ct_position" class="frm_input frm_full">
                        <?php
                        if ($table === 'page') {

                        } else {
                            foreach($configProvider->get('template')['template_position'] as $key=>$val) {
                                if($key == 'left' && ($config_domain['cf_layout'] == 1 || $config_domain['cf_layout'] == 3)) { continue; }
                                if($key == 'right' && ($config_domain['cf_layout'] == 1 || $config_domain['cf_layout'] == 2)) { continue; }
                                $_selected = $templateData['ct_position'] === $key ? 'selected' : '';
                                echo '<option value="'.$key.'" '.$_selected.'>'.$val.'</option>';
                            }
                        }
                        ?>
                        </select>
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">템플릿이 출력될 위치를 지정해 주세요.</span>
                    </div>
                </div>
                <?php if ($table === 'template') { ?>
                <div id="position_sub" class="frm-input-row" style="display:none;">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">출력메뉴</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <select name="ct_position_sub" id="ct_position_sub" class="frm_input frm_full">
                            <option value="">선택하세요</option>
                            <option value="all">전체페이지</option>
                            <?php
                            if (!empty($menuData)) {
                                foreach($menuData as $key=>$val) {
                                    echo '<option value="'.$val['me_code'].'">'.$val['me_name'].'</option>';
                                    if (!empty($val['children'])) {
                                        foreach($val['children'] as $subKey=>$subVal) {
                                            echo '<option value="'.$subVal['me_code'].'">'.$val['me_name'].' > '.$subVal['me_name'].'</option>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="frm-input frm-ml input-prepend frm-auto">
                        <span class="frm_text">하위메뉴 전체적용</span>
                    </div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="ct_position_subview" value="Y" id="ct_position_subview" <?= $templateData['ct_position_subview'] === 'Y' ? 'checked' : '';?>> 예
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">출력될 메뉴를 선택해 주세요.하위 전체 적용 선택 시 해당 메뉴 하위에 모두 적용됩니다.</span>
                    </div>
                </div>
                <?php } ?>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">관리용제목</span>
                    </div>
                    <div class="frm-input wfpe-40">
                        <input type="text" name="ct_admin_subject" class="frm_input frm_full" id="ct_admin_subject" value="<?= $templateData['ct_admin_subject'] ?>">
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">크기설정</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <select name="ct_list_width" id="ct_list_width" class="frm_input frm_full">
                            <option value="0" <?= $templateData['ct_list_width'] === 0 ? 'selected' : ''; ?>>와이드(전체넓이)</option>
                            <option value="1" <?= $templateData['ct_list_width'] === 1 ? 'selected' : ''; ?>>최대넓이(<?= $config_domain['cf_content_max_width']; ?>px)</option>
                        </select>
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">리스트의 최대 넓이를 설정합니다.</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 넓이단위</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <select name="ct_list_box_wtype" id="ct_list_box_wtype" class="frm_input frm_full">
                            <option value="1" <?= (int)$templateData['ct_list_box_wtype'] === 1 ? 'selected' : '';?>>퍼센트(%)</option>
                            <option value="2" <?= (int)$templateData['ct_list_box_wtype'] === 2 ? 'selected' : '';?>>픽셀(px)</option>
                        </select>
                    </div>
                    <div class="frm-input frm-ml input-prepend frm-auto">
                        <span class="frm_text">리스트(1줄) 칸수</span>
                    </div>
                    <div class="frm-input">
                        <select name="ct_list_box_cnt" id="ct_list_box_cnt" class="frm_input">
                            <option value="">칸수를 선택하세요</option>
                            <option value="1" <?= (int)$templateData['ct_list_box_cnt'] === 1 ? 'selected' : '';?>>1칸 출력</option>
                            <option value="2" <?= (int)$templateData['ct_list_box_cnt'] === 2 ? 'selected' : '';?>>2칸 출력</option>
                            <option value="3" <?= (int)$templateData['ct_list_box_cnt'] === 3 ? 'selected' : '';?>>3칸 출력</option>
                            <option value="4" <?= (int)$templateData['ct_list_box_cnt'] === 4 ? 'selected' : '';?>>4칸 출력</option>
                        </select>
                    </div>
                    <div class="frm-input frm-ml input-prepend frm-auto">
                        <span class="frm_text">칸 간격</span>
                    </div>
                    <div class="frm-input wfpx-60">
                        <input type="text" name="ct_list_box_margin" class="frm_input frm_full boxsample" id="ct_list_box_margin" value="<?= $templateData['ct_list_box_margin'] ?>">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">px</span>
                    </div>
                    <div class="frm-input frm-ml">
                        <span class="frm_text">한 줄에 출력될 칸수와 간격 및 넓이 단위를 설정합니다.</span>
                    </div>
                </div>
                <div id="box_width"></div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 높이단위(PC)</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_list_pc_height" class="frm_input frm_full" id="ct_list_pc_height" value="<?= $templateData['ct_list_pc_height'] ?>">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">px OR vh</span>
                    </div>
                    <div class="frm-input frm-ml frm-auto">
                        <span class="frm_text">PC용 - 한 줄에 높이를 설정합니다. 비워두실 경우 자동으로 설정됩니다.입력예) 200px - 단위까지 입력</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 높이단위(MO)</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_list_mo_height" class="frm_input frm_full" id="ct_list_mo_height" value="<?= $templateData['ct_list_mo_height'] ?>">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">px OR vh</span>
                    </div>
                    <div class="frm-input frm-ml frm-auto">
                        <span class="frm_text">모바일용 - 한 줄에 높이를 설정합니다. 비워두실 경우 자동으로 설정됩니다.입력예) 200px - 단위까지 입력</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 내부여백(PC)</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_list_pc_padding" class="frm_input frm_full" id="ct_list_pc_padding" value="<?= $templateData['ct_list_pc_padding'] ?>">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">한 줄의 내부여백을 지정하세요. 입력예) 25px,10px,20px,25px (위여백,오른쪽여백,아래여백,왼쪽여백)</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 내부여백(MO)</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_list_mo_padding" class="frm_input frm_full" id="ct_list_mo_padding" value="<?= $templateData['ct_list_mo_padding'] ?>">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">한 줄의 내부여백을 지정하세요. 입력예) 25px,10px,20px,25px (위여백,오른쪽여백,아래여백,왼쪽여백)</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 전체 배경색상</span>
                    </div>
                    <div class="frm-input wfpx-160">
                        <input type="text" name="ct_list_bgcolor" class="frm_input frm_full color_code" id="ct_list_bgcolor" value="<?= $templateData['ct_list_bgcolor'] ?>" data-position="bottom left">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">한 줄의 전체 배경색상을 선택합니다. 비워두실 경우 기본배경색입니다.</span>
                    </div>
                </div>
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-180">
                        <span class="frm_text">리스트(1줄) 전체 배경이미지</span>
                    </div>
                    <div class="frm-input">
                        <input type="hidden" name="ct_list_old_bgimage" id="ct_list_old_bgimage" value="<?= $templateData['ct_list_bgimage'] ?>">
                        <input type="file" name="ct_list_bgimage" id="ct_list_bgimage" value="" class="frm_input frm_file">
                    </div>
                    <?php if($templateData['ct_list_bgimage']) { ?>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="ct_list_bgimage_del" value="1" id="ct_list_bgimage_del">
                        <label class="ml-1" for="ct_list_bgimage_del">이미지 삭제</label>
                    </div>
                    <?php } ?>
                    <div class="frm-input input-append">
                        <span class="frm_text">한 줄의 전체 배경이미지를 등록합니다.</span>
                    </div>
                </div>
                <!-- 리스트 설정 끝 --->
            </div>
        </div>
    </div>
</div>
</form>
<script src="/assets/js/jquery-3.7.1.min.js"></script>
<script src="/assets/js/jquery-migrate-3.5.0.min.js"></script>
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/lib/color-picker/jquery.minicolors.min.js"></script>
<link rel="stylesheet" href="/assets/js/lib/color-picker/jquery.minicolors.css">
<script>
// 설정 및 상수
const baseConfig = <?= $baseConfig; ?>;
const CONFIG = {
    listItemCount: 17,
    contentWidth: <?= $config_domain['cf_content_max_width']; ?>,
    listEvent: <?= json_encode($configProvider->get('template')['aos_effect']); ?>,
    w: '<?php echo isset($w) ? $w : ''; ?>',
    ...baseConfig,
};

// 유틸리티 함수
const UTIL = {
    numberFormat: (num) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","),
    createOption: (value, text, selected = false) => `<option value="${value}" ${selected ? 'selected' : ''}>${text}</option>`,
    createCheckbox: (name, id, value, label, checked = false) => `
        <div class="frm-input frm-check">
            <input type="checkbox" name="${name}" id="${id}" value="${value}" ${checked ? 'checked' : ''}>
            <label for="${id}">${label}</label>
        </div>
    `,
    // 추가 유틸리티 함수들...
};

// 템플릿 관련 함수들
const TemplateFunctions = {
    // 폼 초기화: 페이지 로드 시 필요한 초기 설정을 수행
    initializeForm() {
        $('#ct_list_box_cnt').trigger('init');
        $('#ct_position').trigger('change');
        if (CONFIG.ct_position_subtype && CONFIG.ct_position_sub) {
            $('#ct_position_sub').val(`${CONFIG.ct_position_subtype}::${CONFIG.ct_position_sub}`).prop('selected', true);
        } else {
            $('#ct_position_sub').val(CONFIG.ct_position_sub).prop('selected', true);
        }
        $('#ct_position_sub').trigger('init');
        $('.ct_list_itemcnt').trigger('init');
        $('.ct_subject_view:checked').trigger('init');
    },
    
    initializeColorPicker() {
        $('.color_code').minicolors('destroy').minicolors({
            change: function(hex, opacity) {
                // 색상 변경 처리
            },
            theme: 'default'
        });
    },
    
    // 위치 변경 처리: 출력 위치가 변경될 때 UI 업데이트
    handlePositionChange() {
        const value = $(this).val();
        $('#position_sub')[value === 'index' ? 'hide' : 'show']();
    },
    
    // 하위 위치 변경 처리: 출력 위치의 세부 설정이 변경될 때 UI 업데이트
    handlePositionSubChange(e) {
        if (e.type === 'init' && CONFIG.ct_position_sub === '') {
            return false;
        }
        const position = $('#ct_position').val();
        const $boxCnt = $('#ct_list_box_cnt');
        const $boxWtype = $('#ct_list_box_wtype');
        const isLeftOrRight = position === 'left' || position === 'right';

        $boxWtype.val(1).prop('selected', true);
        $boxCnt.val(isLeftOrRight ? 1 : this.cntVal).prop('selected', true);

        const options = $boxWtype.find('option').add($boxCnt.find('option'));
        options.prop('disabled', isLeftOrRight).css('color', isLeftOrRight ? '#ccc' : '#000');
        options.filter("[value='1']").prop('disabled', false);

        $('#ct_list_box_margin').val(isLeftOrRight ? '0' : '');

        if (isLeftOrRight && CONFIG.w === '') {
            $boxCnt.trigger('init');
        }
    },
    
    // 제목 표시 여부 변경 처리: 제목 표시 옵션이 변경될 때 UI 업데이트
    handleSubjectViewChange(event) {
        const $this = $(this);
        const $wrap = $this.closest('.list_box');
        const value = $this.val();
        
        // 클릭 이벤트인 경우에만 상태를 변경
        if (event.type === 'click') {
            $wrap.find('.template-subject-wrap')[value === '1' ? 'show' : 'hide']();
        }

        // 'init' 이벤트 시 추가 동작
        if (event.type === 'init') {
            // 초기 상태 설정
            $wrap.find('.template-subject-wrap')[value === '1' ? 'show' : 'hide']();
        }
    },
    
    // 박스 너비 타입 변경 처리: 너비 단위(% 또는 px)가 변경될 때 계산 및 UI 업데이트
    handleListBoxWtype() {
        const $this = $(this);
        const width = $('#ct_list_width').val();
        const value = parseInt($('#ct_list_box_cnt').val());
        const wtype = parseInt($this.val());
        const d_width = parseInt(CONFIG.contentWidth);

        let px = '%';
        let d_value = 100 / value;

        if (!isNaN(d_value) && !Number.isInteger(d_value)) {
            d_value = d_value.toFixed(3);
        }

        if (d_width === 0 && wtype === 2) {
            alert('현재 레이아웃이 전체화면으로 설정되어 있습니다. 단위는 %만 가능합니다.');
            $this.val(1).prop("selected", true);
            return false;
        }

        if (wtype === 2) {
            px = 'px';
            d_value = d_width / value;
            if (!isNaN(d_value) && !Number.isInteger(d_value)) {
                d_value = d_value.toFixed(3);
            }
        }

        $('.ct_list_box_width').val(d_value);
        $('.wtype').html(px);
        $('.boxsample').trigger('init');
    },
    
    // 박스 샘플 생성: 설정된 값에 따라 샘플 박스 HTML 생성
    handleBoxSample() {
        const margin = document.getElementById('ct_list_box_margin').value;
        const wtype = parseInt(document.getElementById('ct_list_box_wtype').value);
        const unit = wtype === 1 ? '%' : 'px';

        const boxWidths = Array.from(document.querySelectorAll('.boxwidth'));
        const html = boxWidths.map((box, index) => {
            const width = box.value;
            const marginLeft = index > 0 ? `margin-left:${margin}px;` : '';
            return `
                <div class="sample_box" style="border:1px solid #ddd;width:${width}${unit};padding:15px 0;text-align:center;${marginLeft}">
                    <span>${width}${unit}</span>
                </div>
            `;
        }).join('');

        document.querySelector('.sample_list').innerHTML = html;
    },
    
    // 박스 개수 변경 처리: 박스 개수가 변경될 때 전체 레이아웃 재계산 및 UI 업데이트
    handleBoxCountChange: function(e) {
        const $this = $(e.target);
        const $wrap = $this.closest('.list-config');
        const pos = $('#ct_position').val();
        const width = $('#ct_list_width').val();
        const margin = $('#ct_list_box_margin').val() || 0;
        const value = parseInt($this.val()); // 줄 수
        const wtype = parseInt($('#ct_list_box_wtype').val());

        if (!pos) {
            alert('출력위치를 선택해 주세요.');
            $this.val('').prop('selected', true);
            return false;
        }

        if (e.type === 'change') {
            TemplateFunctions.resetBoxArrays();
        }

        const dimensionResult = TemplateFunctions.calculateDimensionValues(value, wtype);
        if (!dimensionResult) return false; // 에러 처리

        const { px, d_value } = dimensionResult;

        const shtml = TemplateFunctions.generateSampleHtml(value, margin, px, d_value);
        const { html, whtml } = TemplateFunctions.generateBoxHtml(value, px, d_value);

        $('#box_width').empty().html(whtml + shtml);
        $('.list_box').remove();
        $wrap.after(html);

        TemplateFunctions.initializeColorPicker();
    },
    
    // 치수 값 계산: 주어진 값과 타입에 따라 박스의 치수 값 계산
    calculateDimensionValues(value, wtype) {
        let px = '%';
        let d_value = 100 / value;
        const d_width = parseInt(CONFIG.contentWidth);

        if (!Number.isInteger(d_value)) {
            d_value = parseFloat(d_value.toFixed(3));
        }

        if (d_width === 0 && wtype === 2) {
            alert('와이드(전체화면) 일 경우 단위는 %만 가능합니다.');
            return null;
        }

        if (wtype === 2) {
            px = 'px';
            d_value = d_width / value;
            if (!Number.isInteger(d_value)) {
                d_value = parseFloat(d_value.toFixed(3));
            }
        }

        return { px, d_value };
    },
    
    // 박스 배열 초기화: 설정 변경 시 관련 배열들을 초기화
    resetBoxArrays() {
        CONFIG.boxWidth = [];
        CONFIG.boxItemType = [];
        CONFIG.boxItemCnt = [];
        CONFIG.boxItems = [];
    },
    
    // 샘플 HTML 생성: 계산된 값을 바탕으로 샘플 박스의 HTML 생성
    generateSampleHtml(value, margin, px, d_value) {
        let shtml = '<div class="sample_list mb-2">';
        for (let i = 0; i < value; i++) {
            const marginLeft = i > 0 ? `margin-left:${margin === '0' ? '-1px' : margin + 'px'}` : '';
            const boxWidthValue = CONFIG.boxWidth[i] || d_value;
            shtml += `
                <div class="sample_box" style="border:1px solid #ddd;width:${boxWidthValue}${px};padding: 15px 0;text-align:center;${marginLeft}">
                    <span>${boxWidthValue}${px}</span>
                </div>
            `;
        }
        shtml += '</div>';
        return shtml;
    },
    
    // 박스 HTML 생성: 전체 박스 레이아웃의 HTML 생성
    generateBoxHtml(value, px, d_value) {
        let html = '';
        let whtml = '';
        for (let i = 0; i < value; i++) {
            const boxValues = this.getBoxValues(i, d_value);
            whtml += this.generateWidthHtml(i, boxValues.box_width, px);
            html += this.generateBoxContentHtml(i, boxValues);
        }
        return { html, whtml };
    },
    
    // 박스 값 가져오기: 특정 인덱스의 박스에 대한 모든 설정 값 반환
    getBoxValues(index, defaultValue) {
        return {
            subjectVal: CONFIG.boxSubject[index] || '',
            subjectSizeVal: CONFIG.boxSubjectSize[index] || '16',
            msubjectSizeVal: CONFIG.boxmSubjectSize[index] || '14',
            subjectColorVal: CONFIG.boxSubjectColor[index] || '',
            subjectPosVal: CONFIG.boxSubjectPos[index] || '',
            copytextVal: CONFIG.boxCopytext[index] || '',
            copytextSizeVal: CONFIG.boxCopytextSize[index] || '14',
            mcopytextSizeVal: CONFIG.boxmCopytextSize[index] || '12',
            copytextColorVal: CONFIG.boxCopytextColor[index] || '',
            copytextPosVal: CONFIG.boxCopytextPos[index] || '',
            bgcolorVal: CONFIG.boxBgColor[index] || '',
            bgimageVal: CONFIG.boxBgImage[index] || '',
            pcpaddingVal: CONFIG.boxPcPadding[index] || '',
            mopaddingVal: CONFIG.boxMoPadding[index] || '',
            borderwidthVal: CONFIG.boxBorderWidth[index] || '',
            bordercolorVal: CONFIG.boxBorderColor[index] || '',
            borderroundVal: CONFIG.boxBorderRound[index] || '',
            subjectPcImage: CONFIG.boxSubjectPcImage[index] || '',
            subjectMobileImage: CONFIG.boxSubjectMobileImage[index] || '',
            subjectMoreLink: CONFIG.boxSubjectMoreLink[index] || '',
            subjectMoreUrl: CONFIG.boxSubjectMoreUrl[index] || '',
            boxeffectVal: CONFIG.boxEffect[index] || '',
            box_width: CONFIG.boxWidth[index] || defaultValue,
            itemValue: CONFIG.boxItems[index] || '',
            boxItemType: CONFIG.boxItemType[index] || '',
            boxItemCnt: CONFIG.boxItemCnt[index] || 0,
        };
    },
    
    // 너비 HTML 생성: 각 박스의 너비 설정을 위한 HTML 생성
    generateWidthHtml(index, boxWidth, px) {
        return `
            <div class="frm-input-row">
                <div class="frm-input input-prepend wfpx-180">
                    <span class="frm_text">${index + 1}번째 칸 넓이 설정</span>
                </div>
                <div class="frm-input wfpx-70">
                    <input type="text" name="ct_list_box_width[${index}]" value="${boxWidth}" class="frm_input frm_full boxsample boxwidth">
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text wtype">${px}</span>
                </div>
                <div class="frm-input frm-ml">
                    <span class="frm_text">한칸의 넓이를 설정합니다.</span>
                </div>
            </div>
        `;
    },
    
    // 박스 내용 HTML 생성: 각 박스의 내부 콘텐츠를 위한 HTML 생성
    generateBoxContentHtml(index, boxValues) {
        const {
            itemValue, bgcolorVal, bgimageVal, pcpaddingVal, mopaddingVal,
            borderwidthVal, bordercolorVal, borderroundVal, subjectVal,
            subjectColorVal, subjectSizeVal, msubjectSizeVal, subjectPosVal,
            copytextVal, copytextColorVal, copytextSizeVal, mcopytextSizeVal,
            copytextPosVal, subjectPcImage, subjectMobileImage, subjectMoreLink,
            subjectMoreUrl, boxeffectVal, boxItemType, boxItemCnt
        } = boxValues;
        
        const checked1 = boxValues.boxSubjectView === '1' ? 'checked' : '';
        const checked2 = boxValues.boxSubjectView === '2' || !boxValues.boxSubjectView ? 'checked' : '';
        const subject_style = checked2 ? 'style="display:none;"' : '';

        return `
            <div class="table-row row mb-3 list_box" data-idx="${index}">
                <div class="table-th col-md-2">${index + 1}번째칸 설정</div>
                <div class="table-td col-md-10">
                    <input type="hidden" name="template_items[${index}]" value="${itemValue}" id="template_items_${index}" class="template_items">
                    
                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">내용박스 배경색상</span>
                        </div>
                        <div class="frm-input">
                            <input type="text" name="ct_list_box_bgcolor[${index}]" class="frm_input color_code" id="ct_list_box_bgcolor_${index}" value="${bgcolorVal}" data-position="bottom left">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">내용 박스 배경색상을 선택합니다. 비워두실 경우 기본배경색입니다.</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">내용 박스 배경이미지</span>
                        </div>
                        <div class="frm-input">
                            <input type="hidden" name="ct_list_box_old_bgimage[${index}]" value="${bgimageVal}">
                            <input type="file" name="ct_list_box_bgimage[${index}]" id="ct_list_box_bgimage_${index}" value="" class="frm_input frm_file">
                        </div>
                        ${bgimageVal ? `
                            <div class="frm-input frm-check">
                                <input type="checkbox" name="ct_list_box_bgimage_del[${index}]" value="1" id="ct_list_box_bgimage_del_${index}">
                                <label class="ml-1" for="ct_list_box_bgimage_del_${index}">이미지 삭제</label>
                            </div>
                        ` : ''}
                        <div class="frm-input input-append">
                            <span class="frm_text">내용 박스의 배경이미지를 등록합니다.</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">내부여백(PC)</span>
                        </div>
                        <div class="frm-input">
                            <input type="text" name="ct_list_box_pc_padding[${index}]" class="frm_input frm_full" id="ct_list_box_pc_padding_${index}" value="${pcpaddingVal}">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">PC용 내부여백을 지정하세요. 입력예) 25px,10px,20px,25px (위여백,오른쪽여백,아래여백,왼쪽여백)</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">내부여백(MO)</span>
                        </div>
                        <div class="frm-input">
                            <input type="text" name="ct_list_box_mo_padding[${index}]" class="frm_input frm_full" id="ct_list_box_mo_padding_${index}" value="${mopaddingVal}">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">MOBILE용 내부여백을 지정하세요. 입력예) 25px,10px,20px,25px (위여백,오른쪽여백,아래여백,왼쪽여백)</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">박스테두리</span>
                        </div>
                        <div class="frm-input wfpx-70">
                            <input type="text" name="ct_list_box_border_width[${index}]" class="frm_input frm_full" id="ct_list_box_border_width_${index}" value="${borderwidthVal}">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">박스 테두리 두께를 지정하세요. 단위 px.</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">박스테두리 색상</span>
                        </div>
                        <div class="frm-input wfpx-70">
                            <input type="text" name="ct_list_box_border_color[${index}]" class="frm_input color_code" id="ct_list_box_border_color_${index}" value="${bordercolorVal}">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">내용 박스의 테두리 색상을 선택합니다.</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">박스테두리 라운드</span>
                        </div>
                        <div class="frm-input wfpx-70">
                            <input type="text" name="ct_list_box_border_round[${index}]" class="frm_input frm_full" id="ct_list_box_border_round_${index}" value="${borderroundVal}">
                        </div>
                        <div class="frm-input input-append">
                            <span class="frm_text">px</span>
                        </div>
                        <div class="frm-input frm-ml">
                            <span class="frm_text">내용 박스의 테두리의 라운드를 지정하세요. 숫자만 입력, 단위 px.</span>
                        </div>
                    </div>

                    <div class="frm-input-row">
                        <div class="frm-input input-prepend wfpx-140">
                            <span class="frm_text">제목출력</span>
                        </div>
                        <div class="frm-input frm-check">
                            <input type="radio" name="ct_subject_view[${index}]" value="1" id="ct_subject_view_1_${index}" ${checked1} class="ct_subject_view"> 예
                        </div>
                        <div class="frm-input input-append frm-ml">
                            <span class="frm_text">제목출력안함</span>
                        </div>
                        <div class="frm-input frm-check">
                            <input type="radio" name="ct_subject_view[${index}]" value="2" id="ct_subject_view_2_${index}" ${checked2} class="ct_subject_view"> 예
                        </div>
                    </div>

                    <div class="template-subject-wrap" ${subject_style}>
                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-100">
                                <span class="frm_text">제목</span>
                            </div>
                            <div class="frm-input wfpe-30">
                                <input type="text" class="frm_input frm_full" name="ct_subject[${index}]" value="${subjectVal}" size="45" maxlength="25" placeholder="25자 이내로 입력하세요" >
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">제목색상</span>
                            </div>
                            <div class="frm-input">
                                <input type="text" class="frm_input color_code" name="ct_subject_color[${index}]" value="${subjectColorVal}" data-position="bottom left">
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">제목크기(PC)</span>
                            </div>
                            <div class="frm-input wfpx-50">
                                <input type="text" class="frm_input frm_full" name="ct_subject_size[${index}]" value="${subjectSizeVal}">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">px</span>
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">제목크기(MO)</span>
                            </div>
                            <div class="frm-input wfpx-50">
                                <input type="text" class="frm_input frm_full" name="ct_msubject_size[${index}]" value="${msubjectSizeVal}">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">px</span>
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">제목위치</span>
                            </div>
                            <div class="frm-input wfpx-100">
                                <select name="ct_subject_pos[${index}]" id="ct_subject_pos_${index}" class="frm_input frm_full">
                                    <option value="">위치 선택</option>
                                    <option value="left" ${subjectPosVal === 'left' ? 'selected' : ''}>왼쪽</option>
                                    <option value="center" ${subjectPosVal === 'center' ? 'selected' : ''}>가운데</option>
                                    <option value="right" ${subjectPosVal === 'right' ? 'selected' : ''}>오른쪽</option>
                                </select>
                            </div>
                        </div>

                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-100">
                                <span class="frm_text">문구</span>
                            </div>
                            <div class="frm-input wfpe-30">
                                <input type="text" class="frm_input frm_full" name="ct_copytext[${index}]" value="${copytextVal}" size="45" maxlength="25" placeholder="25자 이내로 입력하세요" >
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">문구색상</span>
                            </div>
                            <div class="frm-input">
                                <input type="text" class="frm_input color_code" name="ct_copytext_color[${index}]" value="${copytextColorVal}" data-position="bottom left">
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">문구크기(PC)</span>
                            </div>
                            <div class="frm-input wfpx-50">
                                <input type="text" class="frm_input frm_full" name="ct_copytext_size[${index}]" value="${copytextSizeVal}">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">px</span>
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">문구크기(MO)</span>
                            </div>
                            <div class="frm-input wfpx-50">
                                <input type="text" class="frm_input frm_full" name="ct_mcopytext_size[${index}]" value="${mcopytextSizeVal}">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">px</span>
                            </div>
                            <div class="frm-input input-prepend frm-ml">
                                <span class="frm_text">문구위치</span>
                            </div>
                            <div class="frm-input wfpx-100">
                                <select name="ct_copytext_pos[${index}]" id="ct_copytext_pos_${index}" class="frm_input frm_full">
                                    <option value="">위치 선택</option>
                                    <option value="left" ${copytextPosVal === 'left' ? 'selected' : ''}>왼쪽</option>
                                    <option value="right" ${copytextPosVal === 'right' ? 'selected' : ''}>오른쪽</option>
                                    <option value="top" ${copytextPosVal === 'top' ? 'selected' : ''}>위쪽</option>
                                    <option value="bottom" ${copytextPosVal === 'bottom' ? 'selected' : ''}>아래쪽</option>
                                </select>
                            </div>
                        </div>

                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-160">
                                <span class="frm_text">PC용 제목 이미지</span>
                            </div>
                            <div class="frm-input">
                                <input type="hidden" name="subject_pc_old_image[${index}]" value="${subjectPcImage}">
                                <input type="file" class="frm_input frm_file" name="ct_subject_pc_image[${index}]">
                            </div>
                            ${subjectPcImage ? `
                                <div class="frm-input frm-check">
                                    <input type="checkbox" name="subject_pc_del_image[${index}]" value="1" id="subject_pc_del_image_${index}">
                                    <label for="subject_pc_del_image_${index}">이미지 삭제</label>
                                </div>
                            ` : ''}
                            <div class="frm-input frm-ml">
                                <span class="frm_text">제목이미지를 등록하실 수 있습니다. 이미지를 제목으로 사용하실 경우 우선 적용됩니다.(PC용)</span>
                            </div>
                        </div>

                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-160">
                                <span class="frm_text">MOBILE용 제목 이미지</span>
                            </div>
                            <div class="frm-input">
                                <input type="hidden" name="subject_mobile_old_image[${index}]" value="${subjectMobileImage}">
                                <input type="file" class="frm_input frm_file" name="ct_subject_mo_image[${index}]">
                            </div>
                            ${subjectMobileImage ? `
                                <div class="frm-input frm-check">
                                    <input type="checkbox" name="subject_mobile_del_image[${index}]" value="1" id="subject_mobile_del_image_${index}">
                                    <label for="subject_mobile_del_image_${index}">이미지 삭제</label>
                                </div>
                            ` : ''}
                            <div class="frm-input frm-ml">
                                <span class="frm_text">모바일용 제목이미지를 등록하실 수 있습니다. 비워두실 경우 PC용 이미지가 적용됩니다.</span>
                            </div>
                        </div>

                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-160">
                                <span class="frm_text">더보기 링크 사용</span>
                            </div>
                            <div class="frm-input frm-check">
                                <input type="checkbox" name="ct_subject_more_link[${index}]" value="1" id="ct_subject_more_link_${index}" ${subjectMoreLink ? 'checked' : ''}>
                                <label for="ct_subject_more_link_${index}">링크사용</label>
                            </div>
                            <div class="frm-input frm-ml input-prepend wfpx-160">
                                <span class="frm_text">더보기 연결 URL</span>
                            </div>
                            <div class="frm-input wfpe-30">
                                <input type="text" name="ct_subject_more_url[${index}]" value="${subjectMoreUrl}" class="frm_input frm_full" id="ct_subject_more_url_${index}">
                            </div>
                        </div>
                    </div>

                    <div class="template-item-group">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-140 input-prepend">
                                <span class="frm_text">출력아이템</span>
                            </div>
                            <div class="frm-input wfpx-180">
                                <select name="ct_list_itemtype[${index}]" id="ct_list_itemtype_${index}" class="frm_input frm_full ct_list_itemtype">
                                    <option value="">선택하세요</option>
                                    ${Object.entries(CONFIG.template_items).map(([key, value]) => 
                                        `<option value="${key}" ${boxItemType === key ? 'selected' : ''}>${value}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">리스트 출력될 아이템의 종류를 설정합니다.</span>
                            </div>
                        </div>
                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-140">
                                <span class="frm_text">출력 이벤트</span>
                            </div>
                            <div class="frm-input wfpx-180">
                                <select name="ct_list_box_effect[${index}]" class="frm_input frm_full">
                                    <option value="">출력이벤트</option>
                                    ${Object.entries(CONFIG.listEvent).map(([key, value]) => 
                                        `<option value="${key}" ${boxeffectVal === key ? 'selected' : ''}>${value}</option>`
                                    ).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-140 input-prepend">
                                <span class="frm_text">출력갯수</span>
                            </div>
                            <div class="frm-input wfpx-180">
                                <select name="ct_list_itemcnt[${index}]" id="ct_list_cnt_${index}" class="frm_input frm_full ct_list_itemcnt" data-idx="${index}">
                                    <option value="">선택하세요</option>
                                    ${Array.from({length: CONFIG.listItemCount - 1}, (_, i) => i + 1).map(j => {
                                        const print_cnt = boxItemCnt > 0 ? boxItemCnt : 0;
                                        const selected = (parseInt(print_cnt) === j) ? 'selected' : '';
                                        const disabled = CONFIG.ruleStr.indexOf(boxItemType) < 0 ? '' : (j === 1 ? '' : 'disabled style="color:#ccc;"');
                                        return `<option value="${j}" ${selected} ${disabled}>${j}개</option>`;
                                    }).join('')}
                                </select>
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text">리스트 출력될 아이템의 갯수를 설정합니다.</span>
                            </div>
                        </div>
                        <div class="item_skin"></div>
                        <div class="item_style"></div>
                    </div>
                    <div class="box_item_wrap"></div>
                    <div id="dummy_content_${index}" class="dummy_content"></div>
                </div>
            </div>
        `;
    },
};

const TemplateHandlers = {
    handleItemTypeChange(e) {
        const $this = $(e.target);
        const $wrap = $this.closest('.table-td');
        const value = $this.val();

        if (e.type === 'change') {
            $wrap.find('.ct_list_itemcnt').val('').prop('selected', true);
            $wrap.find('.item_skin').empty();
            $wrap.find('.item_style').empty();
            $wrap.find('.box_item_wrap').empty();
        }
    },

    updateItemCountOptions($wrap, value) {
        const $itemCount = $wrap.find('.ct_list_itemcnt');
        const isSpecialType = CONFIG.ruleStr.includes(value);

        $itemCount.val(isSpecialType ? 1 : '').prop('selected', true);
        $itemCount.find('option').prop('disabled', isSpecialType).css('color', isSpecialType ? '#ccc' : '#000');
        
        if (isSpecialType) {
            $itemCount.find('option[value="1"]').prop('disabled', false).css('color', '#000');
            $itemCount.trigger('init');
        }
    },

    handleItemCountChange(e) {
        const $this = $(e.target);
        const $parent = $this.closest('.list_box');
        const $wrap = $this.closest('.template-item-group');
        const data = this.getItemData($this, $wrap);

        if (!this.validateItemData(data)) return;

        this.clearPreviousContent($parent);
        this.fetchAndRenderItems(data, $parent);
    },

    getItemData($this, $wrap) {
        return {
            table: $('#table').val() || '',
            ct_id: $('#ct_id').val() || '',
            itemtype: $wrap.find('.ct_list_itemtype').val() || '',
            listcnt: parseInt($this.val()) || 0,
            idx: $this.data('idx'),
            items: CONFIG.boxItems[$this.data('idx')] ? CONFIG.boxItems[$this.data('idx')].split(',') : []
        };
    },

    validateItemData(data) {
        if (!data.table) {
            alert('페이지 분류가 입력되지 않았습니다.');
            return false;
        }
        if (data.itemtype === '' || data.listcnt === 0) {
            alert('출력될 아이템의 종류와 갯수를 선택해 주세요');
            return false;
        }
        return true;
    },

    clearPreviousContent($parent) {
        $parent.find('.box_skin').remove();
        $parent.find('.box_item_wrap').empty();
    },

    async fetchAndRenderItems(data, $parent) {
        try {
            const response = await $.ajax({
                type: "POST",
                data: data,
                url: '/admin/template/templateItem'
            });

            if (response.result === 'failure') {
                throw new Error(response.message);
            }

            TemplateHandlers.renderItemContent(response.data, data, $parent);
        } catch (error) {
            alert(error.message);
        }
    },

    renderItemContent(data, requestData, $parent) {
        TemplateHandlers.renderSkinSelection(data, requestData, $parent);

        if (requestData.itemtype !== 'file') {
            TemplateHandlers.renderStyleOptions(data, requestData, $parent);
        }

        TemplateHandlers.renderSpecificItemType(data, requestData, $parent);
    },
    
    // 스킨 선택 HTML 생성 및 렌더링 로직
    renderSkinSelection(data, requestData, $parent) {
        const { itemtype, idx } = requestData;
        const skinHtml = `
            <div class="frm-input-row box_skin">
                <div class="frm-input input-prepend wfpx-140">
                    <span class="frm_text">${itemtype === 'file' ? '출력 파일' : '출력 스킨'}</span>
                </div>
                <div class="frm-input wfpx-180">
                    <select name="ct_list_box_skin[${idx}]" id="ct_list_box_skin_${idx}" class="frm_input frm_full">
                        <option value="">${itemtype === 'file' ? '출력 파일선택' : '출력 스킨선택'}</option>
                        ${data.skinDir.map(skin => `
                            <option value="${skin.name}" ${data.useskin === skin.name ? 'selected' : ''}>${skin.name}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text">
                        ${itemtype === 'file' 
                            ? '출력할 파일을 설정합니다. 제목 등 모든 설정이 적용되지 않습니다. (파일 경로 /template/widget/file)' 
                            : '출력할 스킨을 설정합니다.'}
                    </span>
                </div>
            </div>
        `;
        $parent.find('.item_skin').html(skinHtml);
    },
    
    // 스타일 옵션 HTML 생성 및 렌더링 로직
    renderStyleOptions(data, requestData, $parent) {
        const { idx, itemtype } = requestData;
        const { boxPcStyle, boxMoStyle, boxPcCols, boxMoCols } = CONFIG;

        const styleHtml = `
            <div class="frm-input-row">
                <div class="frm-input input-prepend wfpx-140">
                    <span class="frm_text">PC용 스타일</span>
                </div>
                <div class="frm-input wfpx-180">
                    <select name="ct_list_box_pcstyle[${idx}]" id="ct_list_box_pcstyle_${idx}" class="frm_input frm_full ct_list_box_pcstyle">
                        <option value="list" ${boxPcStyle[idx] === 'list' ? 'selected' : ''}>리스트형</option>
                        <option value="slide" ${boxPcStyle[idx] === 'slide' ? 'selected' : ''}>슬라이드형</option>
                        <option value="none" ${boxPcStyle[idx] === 'none' ? 'selected' : ''}>숨김</option>
                    </select>
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text">PC에 출력될 리스트 스타일을 설정합니다. 리스트형 또는 슬라이드형만 가능합니다.</span>
                </div>
            </div>
            <div class="frm-input-row">
                <div class="frm-input input-prepend wfpx-140">
                    <span class="frm_text">MOBILE용 스타일</span>
                </div>
                <div class="frm-input wfpx-180">
                    <select name="ct_list_box_mostyle[${idx}]" id="ct_list_box_mostyle_${idx}" class="frm_input frm_full ct_list_box_mostyle">
                        <option value="list" ${boxMoStyle[idx] === 'list' ? 'selected' : ''}>리스트형</option>
                        <option value="slide" ${boxMoStyle[idx] === 'slide' ? 'selected' : ''}>슬라이드형</option>
                        <option value="none" ${boxMoStyle[idx] === 'none' ? 'selected' : ''}>숨김</option>
                    </select>
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text">MOBILE에 출력될 리스트 스타일을 설정합니다. 리스트형 또는 슬라이드형만 가능합니다.</span>
                </div>
            </div>
            <div class="frm-input-row item-rows">
                <div class="frm-input input-prepend wfpx-140">
                    <span class="frm_text">PC용 1줄 출력갯수</span>
                </div>
                <div class="frm-input wfpx-90">
                    <select name="ct_list_box_pccols[${idx}]" id="ct_list_box_pccols_${idx}" class="frm_input frm_full ct_list_box_pccols">
                        ${[1,2,3,4,5,6,7].map(cnt => `
                            <option value="${cnt}" ${(boxPcCols[idx] || 4) == cnt ? 'selected' : ''}>${cnt}개</option>
                        `).join('')}
                        <option value="auto" ${boxPcCols[idx] === 'auto' ? 'selected' : ''}>자동</option>
                    </select>
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text">PC에서 한 줄에 출력할 아이템의 갯수를 입력합니다. 최대 7개까지 가능합니다. 자동을 선택할 경우 출력물의 크기에 맞추어 설정되므로, 일정하지 않습니다.</span>
                </div>
            </div>
            <div class="frm-input-row item-rows">
                <div class="frm-input input-prepend wfpx-140">
                    <span class="frm_text">MOBILE용 1줄 출력갯수</span>
                </div>
                <div class="frm-input wfpx-90">
                    <select name="ct_list_box_mocols[${idx}]" id="ct_list_box_mocols_${idx}" class="frm_input frm_full ct_list_box_mocols">
                        ${[1,2,3,4,5,6,7].map(cnt => `
                            <option value="${cnt}" ${(boxMoCols[idx] || 2) == cnt ? 'selected' : ''}>${cnt}개</option>
                        `).join('')}
                        <option value="auto" ${boxMoCols[idx] === 'auto' ? 'selected' : ''}>자동</option>
                    </select>
                </div>
                <div class="frm-input input-append">
                    <span class="frm_text">MOBILE에서 한 줄에 출력할 아이템의 갯수를 입력합니다. 최대 7개까지 가능합니다. 자동을 선택할 경우 출력물의 크기에 맞추어 설정되므로, 일정하지 않습니다.</span>
                </div>
            </div>
        `;

        $parent.find('.item_style').html(styleHtml);

        if (CONFIG.styleStr.indexOf(itemtype) < 0) {
            $parent.find('.item_style').show();
        } else {
            $parent.find('.item_style').hide();
        }
        
        if (CONFIG.itemStr.indexOf(itemtype) < 0) {
            $(`#template_items_${idx}`).val('');
        }
    },
    
    // 특정 아이템 타입에 따른 렌더링 로직 (예: 이미지, 동영상, 에디터 등)
    renderSpecificItemType(data, requestData, $parent) {
        const itemType = requestData.itemtype;
        const allowItemType = CONFIG.template_items;

        // itemtype이 allowItemType의 키에 속하는지 확인
        if (!Object.keys(allowItemType).includes(itemType)) {
            console.warn("Unsupported item type: " + itemType);
            return false;
        }

        const renderFunction = TemplateHtmls[`render${itemType.charAt(0).toUpperCase() + itemType.slice(1)}`];
        
        if (renderFunction) {
            renderFunction.call(this, data, requestData, $parent);
            return true;
        } else {
            console.warn("Unsupported item type: " + itemType);
            return false;
        }
    },

    // 이미지 등록
    handleImageChange(e) {
        const $this = $(e.target);
        const $wrap = $this.closest('.image-card');
        const $that = $wrap.find('.card-img-label');
        const image = CONFIG.noimg;
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(this.files[0]);
            reader.onload = function (event) {
                var data = event.target.result;
                var node = $that.css('background-image', 'url('+data+')').removeClass('null');
                var image = new Image();
                image.src = data;
            }
        } else {
            $that
                .css('background-image', 'url('+image+')')
                .addClass('null');
        }
    }
    // 기타 템플릿 관련 함수들...
};

/*
 * itemtype 별 html 출력코드
 */
const TemplateHtmls = {
    initializeSortable: function(idx) {
        var $list = $(`#item_asset_${idx}, #item_inven_${idx}`);
        $list.sortable({
            cursor: 'move',
            placeholder: "itemBoxHighlight",
            start: function(event, ui) {
                var newList = oldList = ui.item.parent();
                var item = ui.item;
                var $invens = ui.item.parent(),
                    sortid = $invens.data('id'),
                    itemid = item.attr('id'),
                    nowList = $invens.attr('id'),
                    temp = nowList.split('_'),
                    itemcnt = $(`#item_inven_${sortid}`).data('itemcnt'),
                    area = temp[1];
                if(area == 'asset') {
                    var inven = $(`#item_inven_${sortid} li`);
                    var invencnt = inven.length;
                    $.each(inven, function(i, $i) {
                        var olditem = $i.id;
                        if(itemid == olditem) {
                            alert('이미 진열아이템에 포함된 아이템입니다.');
                            return false;
                        }
                        if(invencnt > (itemcnt - 1)) {
                            alert( '진열아이템은 최대 '+itemcnt+'개까지 가능합니다.');
                            return false;
                        }
                    });
                }
            },
            stop: function(event, ui) {
                var sortid = ui.item.parent().data('id');
                var itemarr = $(`#item_inven_${sortid}`).sortable('toArray'),
                    invencnt = itemarr.length,
                    $wrap = ui.item.closest('.input_item_form');
                $(`#template_items_${sortid}`).val(itemarr.join(','));
                $wrap.find('.item_count').empty().html(invencnt);
            },
            connectWith: ".shift_items"
        }).disableSelection();
    },

    renderAssetOpenHtml(idx, count) {
        return `
            <div class="input_item_form d-flex flex-wrap">
                <div class="shift_item og_item">
                    <div class="goods_shift_option shift_item_count">
                    </div>
                    <ul id="item_asset_${idx}" class="items_asset shift_items list ui-sortable ui-droppable" data-id="${idx}">
        `;
    },

    renderAssetCloseHtml(idx, invenCnt, listCnt, invenHtml) {
        return `
                    </ul>
                </div>
                <div class="shift_arrow">
                    <span><i class="fa fa-angle-double-right" style="font-size: 46px"></i></span>
                </div>
                <div class="shift_item cp_item">
                    <div class="shift_item_count">
                        <span>출력될 아이템</span><b class="item_count">${invenCnt}</b><span>개</span>
                    </div>
                    <ul id="item_inven_${idx}" class="items_inven shift_items list" data-id="${idx}" data-itemcnt="${listCnt}">
                        ${invenHtml}
                    </ul>
                </div>
            </div>
        `;
    },

    renderBoard(data, requestData, $parent) {
        const { idx } = requestData;
        const items = CONFIG.boxItems[idx] ? CONFIG.boxItems[idx].split(',') : [];
        const listcnt = 1;

        let invenHtml = '';
        let invenCnt = 0;
        let html = TemplateHtmls.renderAssetOpenHtml(idx, data.items?.length || 0);

        const boardItems = data?.items || [];

        boardItems.forEach(function($i, i) {
            const itemid = `board_${idx}_${$i.board_id}`;
            let itemHtml = `
                <li id="${itemid}" class="item_small">
                    <dl class="dl">
                        <dt>
                            <p>게시판 명: ${$i.board_name}</p>
                            <p>${$i.board_name} 의 최신글 ${requestData.listcnt} 개가 출력됩니다.</p>
                        </dt>
                    </dl>
                </li>
            `;
            if (items.includes(itemid) && invenCnt < listcnt) {
                invenHtml += itemHtml;
                invenCnt++;
            } else {
                html += itemHtml;
            }
        });

        html += TemplateHtmls.renderAssetCloseHtml(idx, invenCnt, listcnt, invenHtml);

        $parent.find('.box_item_wrap').empty().html(html);

        // Sortable 초기화
        TemplateHtmls.initializeSortable(idx);
    },
    
    renderBoardgroup(data, requestData, $parent) {
        const { idx } = requestData;
        const items = data.display[0].ci_pc_item ? data.display[0].ci_pc_item.split(',') : [];
        const boardItems = data?.items || [];

        let html = '<div class="frm-input-row">';
            html += '<div class="frm-input input-prepend wfpx-140"><span class="frm_text">출력게시판 선택</span></div>';
            boardItems.forEach(function($i, i) {
                const isChecked = items.includes($i.board_id) ? 'checked' : '';
                html += `
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="item_group[${idx}][]" id="item_group_${idx}_${i}" value="${$i.board_id}" ${isChecked}>
                        <label for="item_group_${idx}_${i}">${$i.board_name}</label>
                    </div>
                `;
            });
            html += '</div>';
        html += '</div>';
        $parent.find('.box_item_wrap').empty().html(html);
    },

    renderBanner(data, requestData, $parent) {
        const { idx, listcnt } = requestData;
        const items = CONFIG.boxItems[idx] ? CONFIG.boxItems[idx].split(',') : [];
        
        let invenHtml = '';
        let invenCnt = 0;
        let html = TemplateHtmls.renderAssetOpenHtml(idx, data.items?.length || 0);

        //html += ``;
        
        const bannerItems = data?.items || [];

        bannerItems.forEach(function($i, i) {
            const itemid = `banner_${idx}_${$i.item_uid}`;
            let itemHtml = `
                <li id="${itemid}">
                    <dl class="dl-image">
                        <dt>
                            <img src="${$i.item_image || ''}">
                            <p>
                                ${$i.item_link ? `<a href="${$i.item_link}" target="_blank"><span>연결주소 : ${$i.item_linktxt || ''}</span></a>` : ''}
                                <span>넓이 : ${$i.item_width || '0'}px / 높이 : ${$i.item_height || '0'}px</span>
                            </p>
                        </dt>
                    </dl>
                </li>
            `;

            if (items.includes(itemid) && invenCnt < listcnt) {
                invenHtml += itemHtml;
                invenCnt++;
            } else {
                html += itemHtml;
            }
        });

        //html += '</ul>';

        html += TemplateHtmls.renderAssetCloseHtml(idx, invenCnt, listcnt, invenHtml);

        $parent.find('.box_item_wrap').empty().html(html);

        // Sortable 초기화
        TemplateHtmls.initializeSortable(idx);
    },

    renderImage(data, requestData, $parent) {
        const { idx, listcnt } = requestData;
        const noimg = CONFIG.noimg;

        let html = '<div class="template_image">';
        for (let i = 0; i < listcnt; i++) {
            const item = data?.items?.[i] ?? {};
            const pc_temp_image = item.pc_image_url || noimg;
            const mo_temp_image = item.mo_image_url|| noimg;
            const pc_oldimg = item.pc_image_name || '';
            const mo_oldimg = item.mo_image_name || '';
            const link_url = item.link_url || '';
            const link_win = item.link_win || '';

            html += `
                <div class="template_image_box">
                    <div class="image-box">
                        <input type="hidden" name="image_items[${idx}][${i}]" value="${i}">
                        <div class="image-box-inner">
                            <div class="image-card image-card-${idx}-${i}">
                                <a class="card-img-label card-img-top" style="background-image:url('${pc_temp_image}');"><img src="${noimg}" alt="" style="width:100%;opacity:0;"></a>
                                <div class="image-card-body">
                                    <input type="hidden" name="pc_old_image[${idx}][${i}]" value="${pc_oldimg}">
                                    <input type="hidden" name="pc_del_image[${idx}][${i}]" value="${pc_oldimg}">
                                    <div class="image-card-file">
                                        <input type="file" name="temp_pc_image[${idx}][${i}]" class="custom-file-input temp_image_file" id="custom-pc-file-${idx}-${i}">
                                        <label class="custom-file-label" for="custom-pc-file-${idx}-${i}">PC용 이미지 선택</label>
                                    </div>
                                </div>
                            </div>
                            <div class="image-card image-card-${idx}-${i}">
                                <a class="card-img-label card-img-top" style="background-image:url('${mo_temp_image}');"><img src="${noimg}" alt="" style="width:100%;opacity:0;"></a>
                                <div class="image-card-body">
                                    <input type="hidden" name="mo_old_image[${idx}][${i}]" value="${mo_oldimg}">
                                    <input type="hidden" name="mo_del_image[${idx}][${i}]" value="${mo_oldimg}">
                                    <div class="image-card-file">
                                        <input type="file" name="temp_mo_image[${idx}][${i}]" class="custom-file-input temp_image_file" id="custom-mobile-file-${idx}-${i}">
                                        <label class="custom-file-label" for="custom-mobile-file-${idx}-${i}">MOBILE용 이미지 선택</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-100">
                                <span class="frm_text">연결 URL</span>
                            </div>
                            <div class="frm-input frm-input-full">
                                <input type="text" name="item_link[${idx}][${i}]" value="${link_url}" class="frm_input frm_full">
                            </div>
                        </div>
                        <div class="frm-input-row">
                            <div class="frm-input input-prepend wfpx-100">
                                <span class="frm_text">새창사용</span>
                            </div>
                            <div class="frm-input">
                                <select name="item_win[${idx}][${i}]" class="frm_input">
                                    <option value="">새창사용선택</option>
                                    <option value="0" ${link_win == 0 ? 'selected' : ''}>사용안함</option>
                                    <option value="1" ${link_win == 1 ? 'selected' : ''}>사용함</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        html += '</div>';

        $(`#template_items_${idx}`).val('');
        $parent.find('.box_item_wrap').empty().html(html);
    },
    
    renderMovie(data, requestData, $parent) {
        const { idx, listcnt } = requestData;

        let html = '';
        for (let i = 0; i < listcnt; i++) {
            const movieurl = data.items[i] ? data.items[i].item_name : '';
            html += `
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-140">
                        <span class="frm_text">동영상주소</span>
                    </div>
                    <div class="frm-input wfpe-40">
                        <input type="text" name="item_movie[${idx}][${i}]" value="${movieurl}" class="frm_input frm_full">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">동영상 주소를 입력해 주세요. (UTV만 지원 가능합니다.)</span>
                    </div>
                </div>
            `;
        }

        $parent.find('.box_item_wrap').empty().html(html);
    },
    
    renderEditor(data, requestData, $parent) {
        const { idx } = requestData;
        const content = data.items[0].ci_content ?? '';
        
        let html = '';
        html += `
            <div class="frm-input-row editor-area">
                <div class="frm-input frm-input-full">
                    <textarea name="content[${idx}]" id="content_${idx}" class="editor-form"></textarea>
                </div>
            </div>
        `;

        $parent.find('.box_item_wrap').empty().html(html);
        
        initializeTinyMCE('#content_'+idx, 'basic', false, 300);
        
        setTimeout(function () {
            if (tinymce.get('content_'+idx)) {
                tinymce.get('content_'+idx).setContent(content);
            }
        }, 600);
    },
};

// 이벤트 핸들러
const EventHandlers = {
    setupEventListeners() {
        // 정적 요소에 대한 이벤트 리스너
        $(document).on('change', '#ct_position', TemplateFunctions.handlePositionChange);
        $(document).on('change init', '#ct_position_sub', TemplateFunctions.handlePositionSubChange);
        $(document).on('click init', '.ct_subject_view', TemplateFunctions.handleSubjectViewChange);
        $(document).on('change', '#ct_list_box_wtype', TemplateFunctions.handleListBoxWtype);
        $(document).on('change init', '#ct_list_box_cnt', TemplateFunctions.handleBoxCountChange);
        
        // boxsample 이벤트 리스너 (jQuery를 사용하여 일관성 유지)
        $(document).on('blur init', '.boxsample', TemplateFunctions.handleBoxSample);
        
        // 이미 위임을 사용하고 있는 이벤트들
        $(document).on('change init', '.ct_list_itemtype', TemplateHandlers.handleItemTypeChange.bind(TemplateHandlers));
        $(document).on('change init', '.ct_list_itemcnt', TemplateHandlers.handleItemCountChange.bind(TemplateHandlers));

        $(document).on('change', '.temp_image_file', TemplateHandlers.handleImageChange);
    
        // 기타 이벤트 리스너들...
    },
};

// 초기화 및 메인 실행
$(document).ready(function() {
    EventHandlers.setupEventListeners();
    TemplateFunctions.initializeForm();
});

// 폼 제출 함수 (전역 함수로 유지, PHP에서 호출됨)
function ftemplateform_submit() {
  return true;
}
</script>