<div class="page-container">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn"></div>
        </div>
    </div>
    <div class="table-flex flex-wrap table-container">
        <div class="col-12 col-md-7 order-2 order-md-1 table-container">
            <div class="form-title table-flex justify-between">
                <span>등급별 권한 목록</span>
                <select name="levelSearch" id="levelSearch">
                    <option value="">관리자 등급선택</option>
                    <?php
                    foreach($levelData as $key=>$val) {
                        if ($val['is_admin'] == 0 || $val['is_super'] == 1) {
                            continue;
                        }
                        echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <form name="flist" id="flist">
            <div class="table-list-container">
                <ul class="table-list-wrapper">
                    <li class="table-list-row list-head">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">
                                <input type="checkbox" name="listCheckAll" value="1" id="listCheckAll" class="list-check-all">
                                <label for="listCheckAll" class="sound-only">선택</label>
                            </div>
                            <div class="list-col col-custom-80 text-center">등급</div>
                            <div class="list-col col-custom-120 text-center">회원등급명</div>
                            <div class="list-col col-custom-120 text-center">1단계 메뉴명</div>
                            <div class="list-col col-custom-120 text-center">적용 메뉴명</div>
                            <div class="list-col col-custom-auto text-center">권한</div>
                            <div class="list-col list-col-row col-custom-100 text-center">관리</div>
                        </div>
                    </li>
                    <?php
                    if (!empty($authData)) {
                        $index = 0;
                        foreach($authData as $key=>$val) {
                            $levelAuthData = $val;
                            foreach($levelAuthData as $levelAuth) {
                                $menu = isset($menuData[$levelAuth['menuCate']]) ? $menuData[$levelAuth['menuCate']] : [];
                                if (empty($menu)) {
                                    continue;
                                }
                                $subMenu = array_filter($menu['submenu'], function($item) use ($levelAuth) {
                                    return $item['code'] === $levelAuth['menuCode'];
                                });
                                $subMenu = array_values($subMenu);
                                $subMenu = !empty($subMenu) ? $subMenu[0] : null;
                                if ($subMenu === null) {
                                    continue;
                                }
                                $menuAuthArray = $levelAuth['menuAuth'] ? explode(",", $levelAuth['menuAuth']) : [];
                                echo '<li class="table-list-row list-body list-auth" data-bunch="'.$index.'" data-level="'.$levelAuth['level_id'].'">';
                                    echo '<div class="list-row">';
                                        echo '<div class="list-col col-custom-60 text-center">';
                                            echo '<input type="checkbox" name="itemNo['.$index.']" value="'.$levelAuth['no'].'" id="check_'.$index.'" class="list-check">';
                                            echo '<label for="check_'.$index.'" class="sound-only">선택</label>';
                                        echo '</div>';
                                        echo '<div class="list-col col-custom-80 text-center">'.$levelAuth['level_id'].'</div>';
                                        echo '<div class="list-col col-custom-120 text-center">'.$levelData[$levelAuth['level_id']]['level_name'].'</div>';
                                        echo '<div class="list-col col-custom-120 text-center">'.$menu['label'].'</div>';
                                        echo '<div class="list-col col-custom-120 text-center">'.$subMenu['label'].'</div>';
                                        echo '<div class="list-col col-custom-auto text-center">';
                                            echo '<div class="frm-input-row justify-center">';
                                            foreach(['r','w','d'] as $authKey=>$authVal) {
                                                $authName = '';
                                                if ($authVal === 'r') {
                                                    $authName = '읽기';
                                                }
                                                if ($authVal === 'w') {
                                                    $authName = '쓰기';
                                                }
                                                if ($authVal === 'd') {
                                                    $authName = '삭제';
                                                }
                                                $checked = in_array($authVal, $menuAuthArray) ? 'checked' : '';
                                                $proto = in_array($authVal, $menuAuthArray) ? $authVal : '';

                                                echo '<div class="frm-input frm-check">';
                                                    echo '<input type="checkbox" name="menuAuth['.$index.'][]" id="list_menuAuth_'.$levelAuth['menuCode'].'_'.$authVal.'" value="'.$authVal.'" data-proto="'.$proto.'" '.$checked.'>';
                                                    echo '<label for="list_menuAuth_'.$levelAuth['menuCode'].'_'.$authVal.'">'.$authName.'</label>';
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        echo '</div>';
                                        echo '<div class="list-col list-col-row col-custom-100 text-center">';
                                            echo '<button type="button" class="btn btn-ssm btn-fill-accent" data-data=\''.json_encode($levelAuth).'\' data-label="'.$subMenu['label'].'" data-code onclick="loaderData(this);">수정</button>';
                                            echo '<button type="button" class="btn btn-ssm btn-fill-darkgray ml-1" onclick="confirmDeleteBefore(this);" data-target="/admin/members/memberAuthDelete/'.$levelAuth['no'].'?menuCode='.$levelAuth['menuCode'].'" data-callback="updateMemberAuthDelete" data-message="관리자의 권한을 삭제하시겠습니까?">삭제</button>';
                                        echo '</div>';
                                    echo '</div>';
                                echo '</li>';
                                $index++;
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="table-flex mt-3">
                <button type="button" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/members/memberAuthListModify" data-callback="updateProcess">선택수정</button>
                <button type="button" class="btn btn-fill-darkgray ml-1" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/members/memberAuthListDelete" data-callback="updateProcess">선택삭제</button>
            </div>
            </form>
        </div>
        <div class="col-12 col-md-5 order-1 order-md-2 mb-md-0 table-container px-3">
            <form name="frm" id="frm">
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="group_no" value="" id="group_no">
            <div class="table-form table-form-md">
                <h2 class="form-title">관리자 권한 등록/수정</h2>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>관리자 등급</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-160">
                                <select name="level_id" id="levelId">
                                    <option value="">관리자 등급선택</option>
                                    <?php
                                    foreach($levelData as $key=>$val) {
                                        if ($val['is_admin'] == 0 || $val['is_super'] == 1) {
                                            continue;
                                        }
                                        echo '<option value="'.$val['level_id'].'">'.$val['level_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>메뉴 분류</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input wfpx-160">
                                <select name="menuCate" id="menuCate">
                                    <option value="">1단계 메뉴선택</option>
                                    <?php
                                    foreach($menuData as $key=>$val) {
                                        echo '<option value="'.$key.'">'.$val['label'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <span>접근가능 메뉴</span>
                    </div>
                    <div class="table-td col-md-8">
                        <div id="auth-menu-wrap"></div>
                    </div>
                </div>
                <div class="table-flex justify-end mt-3">
                    <button type="button" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/members/memberAuthUpdate" data-callback="updateProcess">확인</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
const menuData = <?php echo json_encode($menuData); ?>;
const authData = <?php echo json_encode($authData); ?>;

document.addEventListener('DOMContentLoaded', function () {
    const levelSearch = document.getElementById('levelSearch');
    const levelSelector = document.getElementById('levelId');
    const menuSelector = document.getElementById('menuCate');

    levelSearch.addEventListener('change', function () {
        const selectedLevel = levelSearch.value;

        // 모든 .list-auth 요소 가져오기
        const authRows = document.querySelectorAll('.list-auth');

        if (!selectedLevel) {
            // 선택된 값이 없으면 모든 행을 표시
            authRows.forEach(row => {
                row.style.display = ''; // 모든 요소를 표시
            });
            return;
        }

        authRows.forEach(row => {
            // data-level 값 가져오기
            const level = row.getAttribute('data-level');
            console.log(level);

            // 선택된 level과 일치하는 경우 보여주고, 그렇지 않으면 숨기기
            if (level === selectedLevel) {
                row.style.display = ''; // 표시
            } else {
                row.style.display = 'none'; // 숨기기
            }
        });
    });

    menuSelector.addEventListener('change', function () {
        const selectedLevel = levelSelector.value;
        const selectedMenu = this.value;

        if (!selectedLevel || !selectedMenu) {
            alert('관리자 등급과 메뉴 분류를 모두 선택해 주세요');
            return false;
        }

        // selectedLevel과 일치하는 authData 항목이 있는지 확인
        const levelAuths = authData[selectedLevel] || [];

        // selectedMenu 값에 해당하는 서브메뉴 가져오기
        const menuItem = menuData[selectedMenu];
        if (!menuItem || !menuItem.submenu) {
            alert('선택한 메뉴에 서브메뉴가 없습니다.');
            return false;
        }

        // 서브메뉴 항목들을 반복하여 HTML 생성
        const html = Object.keys(menuItem.submenu).map((subKey, i) => {
            const subMenu = menuItem.submenu[subKey];
            
            // authData에서 현재 subMenu와 일치하는 메뉴 권한을 찾음
            const menuAuthData = levelAuths.find(item => item.menuCode === subMenu.code);
            
            // menuCode 체크박스의 체크 여부 설정
            const menuCodeChecked = menuAuthData ? 'checked' : '';

            // menuAuth 값에 따라 권한 체크 여부 설정
            let r_checked = '';
            let w_checked = '';
            let d_checked = '';
            if (menuAuthData) {
                const authArray = menuAuthData.menuAuth.split(',');
                r_checked = authArray.includes('r') ? 'checked' : '';
                w_checked = authArray.includes('w') ? 'checked' : '';
                d_checked = authArray.includes('d') ? 'checked' : '';
            }

            return `
                <div class="frm-input-row" style="padding-bottom:8px;border-bottom:1px dotted #ddd;">
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="menuCode[${i}]" id="menuCode_${i}" value="${subMenu.code}" ${menuCodeChecked}>
                        <label for="menuCode_${i}">${subMenu.label}</label>
                    </div>
                    <div class="frm-input frm-check frm-ml-auto">
                        <input type="checkbox" name="menuAuth[${i}][]" id="menuAuth_r_${i}" value="r" ${r_checked}>
                        <label for="menuAuth_r_${i}">읽기</label>
                    </div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="menuAuth[${i}][]" id="menuAuth_w_${i}" value="w" ${w_checked}>
                        <label for="menuAuth_w_${i}">쓰기</label>
                    </div>
                    <div class="frm-input frm-check">
                        <input type="checkbox" name="menuAuth[${i}][]" id="menuAuth_d_${i}" value="d" ${d_checked}>
                        <label for="menuAuth_d_${i}">삭제</label>
                    </div>
                </div>
            `;
        }).join('');

        // 생성된 HTML을 원하는 위치에 삽입
        document.getElementById('auth-menu-wrap').innerHTML = html;
    });
});

function loaderData(button) {
    const data = JSON.parse(button.getAttribute('data-data'));
    const label = button.getAttribute('data-label');
    
    // level_id에 해당하는 셀렉트 박스 선택
    const levelIdSelect = document.getElementById('levelId');
    levelIdSelect.value = data.level_id;

    // level_id에 해당하는 셀렉트 박스 선택
    const menuCateSelect = document.getElementById('menuCate');
    menuCateSelect.value = data.menuCate;

    // menuAuth 문자열을 배열로 분리
    const authArray = data.menuAuth.split(',');

    // 각 권한에 대해 체크 여부 설정
    const r_checked = authArray.includes('r') ? 'checked' : '';
    const w_checked = authArray.includes('w') ? 'checked' : '';
    const d_checked = authArray.includes('d') ? 'checked' : '';
    
    let html = `
        <div class="frm-input-row">
            <div class="frm-input frm-check">
                <input type="checkbox" name="menuCode[0]" id="menuCode_0" value="${data.menuCode}" checked>
                <label for="menuCode_0">${label}</label>
            </div>
            <div class="frm-input frm-check frm-ml-auto">
                <input type="checkbox" name="menuAuth[0][]" id="menuAuth_r_0" value="r" ${r_checked}>
                <label for="menuAuth_r_0">읽기</label>
            </div>
            <div class="frm-input frm-check">
                <input type="checkbox" name="menuAuth[0][]" id="menuAuth_w_0" value="w" ${w_checked}>
                <label for="menuAuth_w_0">쓰기</label>
            </div>
            <div class="frm-input frm-check">
                <input type="checkbox" name="menuAuth[0][]" id="menuAuth_d_0" value="d" ${d_checked}>
                <label for="menuAuth_d_0">삭제</label>
            </div>
        </div>
    `;

    // 생성된 HTML을 원하는 위치에 삽입
    document.getElementById('auth-menu-wrap').innerHTML = html;
}

App.registerCallback('updateProcess', function(data) {
    location.reload();
});

App.registerCallback('updateMemberAuthDelete', function(data) {
    location.reload();
});
</script>