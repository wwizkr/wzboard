<form name="frm" id="frm" enctype="multipart/form-data">
<input type="hidden" name="ba_id" id="ct_id" value="<?= $data['ba_id']; ?>">

<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?= $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/banner/bannerList" class="btn btn-fill-darkgray">배너 목록</a>
            <button type="button" value="확인" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/banner/bannerUpdate" data-callback="updateBanner">확인</button>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="table-form">
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>출력</label>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-140">
                        <select name="formData[ba_use]" id="ba_use" class="frm_input frm_full">
                            <option value="1">출력함</option>
                            <option value="2">출력안함</option>
                        </select>
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">배너 출력여부입니다. 상단 배너에만 적용됩니다.</span>
                    </div>
                </div>
            </div>
            <div class="table-th col-md-2">
                <span>사용</span>
            </div>
            <div class="table-td col-md-4">
                <div class="frm-input-row">
                    <div class="frm-input"><input type="text" name="formData[ba_order]" id="ba_order" class="frm_input frm_num"></div>
                    <div class="frm-input input-append">
                        <span class="frm_text">낮은 순번이 먼저 출력됩니다. 상단 배너에만 적용됩니다.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>출력 위치</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-140">
                        <select name="formData[ba_position]" id="ba_position" class="frm_input frm_full">
                            <option value="">배너위치 선택</option>
                            <option value="상단">상단</option>
                            <option value="내용">내용</option>
                        </select>
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">배너를 표시할 영역입니다. 상단=> 홈페이지 최상단에 노출, 내용=> 디자인 관리 -> 템플릿 관리에서 등록 가능</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>배너 이미지</label>
            </div>
            <div class="table-td col-md-10">
                <div class="row">
                    <div class="image-wrap col-12 col-md-4 mb-3 mb-md-0">
                        <h5>PC용 이미지 등록</h5>
                        <div class="frm-input-row">
                            <div class="frm-input frm-file">
                                <label for="pc_image">파일 선택</label>
                                <input type="file" name="fileData[banner_image][pc]" id="pc_image" class="image-selector">
                            </div>
                            <div class="frm-input frm-ml input-prepend ">
                                <span class="frm_text">배경색</span>
                            </div>
                            <div class="frm-input">
                                <input type="text" name="formData[ba_pc_bgcolor]" id="pc_bg_color" class="frm_input frm_full color_code" value="">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text color_remove" style="cursor:pointer;">제거하기</span>
                            </div>
                        </div>
                        <div class="banner-image pc"></div>
                    </div>
                    <div class="image-wrap col-12 col-md-4 mb-3 mb-md-0">
                        <h5>모바일용 이미지 등록</h5>
                        <div class="frm-input-row">
                            <div class="frm-input frm-file">
                                <label for="mo_image">파일 선택</label>
                                <input type="file" name="fileData[banner_image][mo]" id="mo_image" class="image-selector">
                            </div>
                            <div class="frm-input frm-ml input-prepend ">
                                <span class="frm_text">배경색</span>
                            </div>
                            <div class="frm-input">
                                <input type="text" name="formData[ba_mo_bgcolor]" id="mo_bg_color" class="frm_input frm_full color_code" value="">
                            </div>
                            <div class="frm-input input-append">
                                <span class="frm_text color_remove" style="cursor:pointer;">제거하기</span>
                            </div>
                        </div>
                        <div class="banner-image mo"></div>
                    </div>
                    <div class="image-wrap col-12 col-md-4">
                        <h5>배경 이미지 등록</h5>
                        <div class="frm-input-row">
                            <div class="frm-input frm-file">
                                <label for="bg_image">파일 선택</label>
                                <input type="file" name="fileData[banner_image][bg]" id="bg_image" class="image-selector">
                            </div>
                        </div>
                        <div class="banner-image bg"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>유투브 영상 URL</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpe-40">
                        <input type="text" name="formData[ba_utv_url]" value="" id="ba_utv_url" class="frm_input frm_full">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">UTV 영상을 배경으로 사용합니다. UTV URL을 입력해 주세요. 배너 이미지는 적용되지 않습니다.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>새창</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input input-prepend wfpx-120">
                        <select name="formData[ba_new_win]" id="ba_new_win" class="frm_input frm_full">
                            <option value="0">사용안함</option>
                            <option value="1">사용</option>
                        </select>
                    </div>
                    <div class="frm-input">
                        <span class="frm_text">배너 클릭시 새창을 띄울지를 설정합니다.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>시작일</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[ba_begin_time]" value="" id="ba_begin_time" class="frm_input frm_full datepicker"  maxlength="19">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">배너 게시 시작일시를 설정합니다. 상단 배너에만 적용됩니다.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-row row mb-3">
            <div class="table-th col-md-2">
                <span>종료일</label>
            </div>
            <div class="table-td col-md-10">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-160">
                        <input type="text" name="formData[ba_end_time]" value="" id="ba_end_time" class="frm_input frm_full datepicker"  maxlength="19">
                    </div>
                    <div class="frm-input input-append">
                        <span class="frm_text">배너 게시 종료일시를 설정합니다. 상단 배너에만 적용됩니다.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
var bannerData = <?php echo json_encode($data); ?>;
var noImage430 = '<?= $noImage430; ?>';

document.addEventListener('DOMContentLoaded', function() {
    fillFormData(bannerData, 'formData', fillImage);

    // 이미지 선택기에 이벤트 리스너 추가
    const imageSelectors = document.querySelectorAll('.image-selector');
    imageSelectors.forEach(selector => {
        selector.addEventListener('change', handleImageSelect);
    });

    // 삭제 체크박스에 이벤트 리스너 추가
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name && e.target.name.startsWith('banner_del[')) {
            handleImageDelete(e.target);
        }
    });

    // jQuery가 로드되었는지 확인 후 컬러 피커와 데이트피커 초기화
    if (typeof jQuery !== 'undefined') {
        if (jQuery.fn.minicolors) {
            initializeColorPicker();
        } else {
            console.error('minicolors plugin is not loaded');
        }
        
        if (jQuery.fn.datepicker) {
            initializeDatepicker();
        } else {
            console.error('jQuery UI Datepicker is not loaded');
        }
    } else {
        console.error('jQuery is not loaded');
    }
});

function fillImage() {
    if (!bannerData || !bannerData.images) {
        console.error('Banner data is not available');
        return;
    }

    const bannerTypes = ['pc', 'mo', 'bg'];
    
    bannerTypes.forEach(type => {
        const element = document.querySelector(`.banner-image.${type}`);
        if (!element) {
            console.warn(`Element for banner type '${type}' not found`);
            return;
        }

        const imageData = bannerData.images[type];
        if (!imageData || !imageData.url) {
            console.warn(`No image data for ${type}`);
            return;
        }

        let html = `<div><img src="${imageData.url}" class="image-preview" style="width:100%;height:auto;"></div>`;
        
        if (imageData.del !== false) {
            html += `
                <div class="frm-input-row">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="banner_del[${type}]" value="1" id="${type}_img_del">
                        <label for="${type}_img_del">삭제</label>
                    </div>
                </div>
            `;
        }

        element.innerHTML = html;
    });
}

function handleImageSelect(event) {
    const file = event.target.files[0];
    const allowedMimeTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/webp'];
    const allowedExtensions = ['gif', 'jpg', 'jpeg', 'png', 'webp'];
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (!file) return;

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!allowedMimeTypes.includes(file.type) || !allowedExtensions.includes(fileExtension)) {
        alert('허용되지 않는 파일 형식입니다. gif, jpg, jpeg, png, webp 형식만 가능합니다.');
        event.target.value = ''; // 선택된 파일 초기화
        return;
    }

    if (file.size > maxSize) {
        alert('파일 크기가 너무 큽니다. 5MB 이하의 파일만 가능합니다.');
        event.target.value = ''; // 선택된 파일 초기화
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const type = event.target.id.split('_')[0]; // pc, mo, bg
            const bannerImageElement = document.querySelector(`.banner-image.${type}`);
            if (bannerImageElement) {
                updateBannerImage(bannerImageElement, e.target.result, type);
            }
            // 이미지를 선택했으므로 삭제 체크박스를 해제합니다
            const deleteCheckbox = document.getElementById(`${type}_img_del`);
            if (deleteCheckbox) {
                deleteCheckbox.checked = false;
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function handleImageDelete(checkbox) {
    const type = checkbox.name.match(/\[(.*?)\]/)[1]; // pc, mo, bg
    const bannerImageElement = document.querySelector(`.banner-image.${type}`);
    const fileInput = document.getElementById(`${type}_image`);

    if (checkbox.checked) {
        // 삭제가 체크되면 기본 이미지로 변경하고 파일 입력을 초기화합니다
        if (bannerImageElement) {
            updateBannerImage(bannerImageElement, noImage430, type);
        }
        if (fileInput) {
            fileInput.value = ''; // 파일 입력 초기화
        }
    } else {
        // 체크가 해제되면 원래 이미지로 복원합니다 (있다면)
        if (bannerData && bannerData.images && bannerData.images[type]) {
            const originalImage = bannerData.images[type].url;
            updateBannerImage(bannerImageElement, originalImage, type);
        }
    }
}

function updateBannerImage(element, imageSrc, type) {
    element.innerHTML = `
        <div>
            <img src="${imageSrc}" class="image-preview" style="width:100%;height:auto;">
        </div>
        <div class="frm-input-row">
            <div class="frm-input frm-check">
                <input type="checkbox" name="banner_del[${type}]" value="1" id="${type}_img_del">
                <label for="${type}_img_del">삭제</label>
            </div>
        </div>
    `;
}

App.registerCallback('updateBanner', function(data) {
    console.log(data);
});
</script>