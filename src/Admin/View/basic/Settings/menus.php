<link rel="stylesheet" href="/assets/js/lib/ztree/css/metroStyle/metroStyle.css" />

<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
        </div>
    </div>
</div>
<div class="page-container">
    <div class="table-flex table-container">
        <!-- 메뉴 트리 영역 -->
        <div class="col-12 col-md-4 order-2 order-md-1">
            <h2 class="form-title">메뉴 맵</h2>
            <div class="table-roll">
                <ul id="menuTree" class="ztree" style="max-height:560px;overflow-y:auto;"></ul>
            </div>
            <div class="table-flex justify-end mt-4">
                <button type="button" class="btn btn-fill-accent" id="add_depth1">
                    <i class="fa fa-fw fa-lg fa-folder" aria-hidden="true"></i> 1단계 메뉴 추가
                </button>
                <button type="button" class="btn btn-fill-gray ml-3" id="add_sub">
                    <i class="fa fa-fw fa-lg fa-star" aria-hidden="true"></i> 하위 메뉴 추가
                </button>
            </div>
        </div>
        <!-- 입력폼 영역 -->
        <div class="col-12 col-md-4 order-1 order-md-2 px-3">
            <form name="frm" id="frm">
            <input type="hidden" name="no" value="" id="no">
            <input type="hidden" name="me_code" value="" id="me_code">
            <div class="table-form table-form-md">
                <h2 class="form-title">메뉴 설정</h2>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="me_cate1" class="form-label">그룹아이디</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input frm-input-full">
                                <select name="formData[me_cate1]" id="me_cate1" class="frm_input frm_full">
                                    <option value="">메뉴 분류 선택</option>
                                    <?php
                                    foreach($menuCategory as $key=>$val) {
                                        echo '<option value="'.$key.'">'.$val['title'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="me_cate2" class="form-label">메뉴 선택</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input frm-input-full">
                                <select name="formData[me_cate2]" id="me_cate2" class="frm_input frm_full">
                                    <option value="">메뉴 선택</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="me_name" class="form-label">메뉴명</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input frm-input-full">
                                <input type="text" name="formData[me_name]" id="me_name" class="frm_input frm_full require" value="" data-type="text" data-message="메뉴명은 필수입니다.">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-row">
                    <div class="table-th col-md-4">
                        <label for="me_link" class="form-label">메뉴 연결주소</label>
                    </div>
                    <div class="table-td col-md-8">
                        <div class="frm-input-row">
                            <div class="frm-input frm-input-full">
                                <input type="text" name="formData[me_link]" id="me_link" class="frm_input frm_full require" value="" data-type="text" data-message="연결주소는 필수입니다.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-flex justify-end mt-3">
                <button type="button" class="btn btn-fill-accent" onclick="javascript:handleAjaxFormSubmit(this);" data-target="/admin/settings/menuUpdate" data-callback="updateMenuTree">확인</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/jquery-3.7.1.min.js"></script>
<script src="/assets/js/jquery-migrate-3.5.0.min.js"></script>
<script src="/assets/js/lib/ztree/js/jquery.ztree.all.min.js"></script>
<script>
var menuData = <?= json_encode($menuDatas); ?>;
var menuCategory = <?= json_encode($menuCategory); ?>;

function addDisplayNameToNodes(nodes) {
    return nodes.map(function(node) {
        let prefix = '';

        // 하위 메뉴일 경우, depth에 따라 접두사를 추가
        if (node.me_depth > 1) {
            prefix = '- '.repeat(node.me_depth - 1); // depth에 따라 "-"를 반복
        }

        // 현재 노드에 displayName 추가
        node.displayName = prefix + node.me_name + " (" + node.me_code + ")";

        // 2단계까지 메뉴를 기본으로 펼치기
        node.open = node.me_depth <= 2;  // me_depth가 2 이하이면 펼침

        // 자식 노드가 있다면 재귀적으로 처리
        if (node.children && node.children.length > 0) {
            node.children = addDisplayNameToNodes(node.children);
        }

        return node;
    });
}
var treeId = "menuTree";

// 기본 root 노드 설정
var rootNode = {
    me_name: "Root",
    me_code: 0,
    no: 0,
    me_parent: 0,
    me_depth: 0,
    open: true,
    type: "root",
    isParent: true,
};
var zNodes = <?php echo json_encode($menuDatas); ?>;
zNodes.unshift(rootNode);
zNodes = addDisplayNameToNodes(zNodes);

$(document).ready(function(){
    $.fn.zTree.init($("#" + treeId), getTreeSetting(), zNodes);

    $('#add_depth1').on('click', function() {
        var zTree = $.fn.zTree.getZTreeObj(treeId);
        var rootNode = zTree.getNodeByParam('me_code', 0);
        zTree.selectNode(rootNode);

        $('#addBtn_' + rootNode.tId).trigger('click');
    });

    $('#add_sub').on('click', function() {
        var zTree = $.fn.zTree.getZTreeObj(treeId);
        var nodes = zTree.getSelectedNodes();
        if (nodes.length > 0) {
            $('#addBtn_' + nodes[0].tId).trigger('click');
        }
    });

    $('#menuUpateButton').on('click', function() {
        menuUpdate($('#frm')[0]);
    });
});

function getTreeSetting() {
    return {
        data: {
            simpleData: {
                enable: true, // 단순 데이터 형식을 사용할 경우 true로 설정
                idKey: "me_code", // 각 노드의 고유 ID에 매핑될 속성
                pIdKey: "me_parent", // 부모 노드를 지정할 속성
                rootPId: 0 // 최상위 루트 노드의 pId 값 (보통 0)
            },
            key: {
                name: "displayName" // 'me_name'을 'name'으로 사용하도록 지정
            }
        },
        edit: {
            enable: true,
            showRemoveBtn: showRemoveBtn,
            showRenameBtn: showRenameBtn,
            drag: {
                prev: dropPrev,
                next: dropNext,
                inner: dropInner
            }
        },
        view: {
            showTitle: false,
            selectedMulti: false,
            addHoverDom: addHoverDom,
            removeHoverDom: removeHoverDom,
            dblClickExpand: dblClickExpand
        },
        callback: {
            beforeDrag: beforeDrag,
            beforeDrop: beforeDrop,
            onClick: categoryLoader,
            onDrop: menuOrder,
            beforeEditName: beforeEditName,
            //onRename: categoryRename,
            beforeRemove: beforeRemove,
            onRemove: categoryRemove
        }
    };
}

function addHoverDom(treeId, treeNode) {
    if (treeNode.level > 5) return false;
    var sObj = $("#" + treeNode.tId + "_span");
    if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0) return;

    var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
        + "' title='추가' onfocus='this.blur();'></span>";
    sObj.after(addStr);

    var btn = $("#addBtn_" + treeNode.tId);
    if (btn) btn.bind("click", async function(event){
        event.stopPropagation();

        var zTree = $.fn.zTree.getZTreeObj(treeId);
        
        var newNodeName = treeNode.level === 0 ? "1단계 메뉴명" : "하위메뉴명";
        var requestData = {
            type: treeNode.type,
            me_name: newNodeName,
            me_code: treeNode.me_code,
            me_parent: treeNode.me_parent,
        };
        if (treeNode.level === 0) {
            requestData.me_depth = 1;
        } else {
            requestData.me_depth = treeNode.me_depth + 1;
        }

        try {
            const data = await sendCustomAjaxRequest('POST', '/admin/settings/menuInsert', requestData, true);
            if (data.result === "success") {
                var newNode = data.data;
                
                var addedNode = zTree.addNodes(treeNode, {
                    me_name: newNode.me_name,
                    isParent: newNode.isParent,
                    open: newNode.open,
                    type: "sub",
                    no: newNode.no,
                    me_code: newNode.me_code,
                    me_depth: newNode.me_depth,
                    me_parent: newNode.me_parent,
                    displayName: newNode.me_name + " (" + newNode.me_code + ")"
                });
                zTree.selectNode(addedNode[0]);
                categoryLoader(null, treeId, addedNode[0]);
            } else {
                console.error("Failed to add menu node:", data.message);
                alert('메뉴 추가에 실패했습니다: ' + data.message);
            }
        } catch (error) {
            console.error("Error:", error);
            alert('메뉴 추가 중 오류가 발생했습니다: ' + error.message);
        }
    });
}

function removeHoverDom(treeId, treeNode) {
    $("#addBtn_" + treeNode.tId).unbind().remove();
}

async function categoryLoader(event, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    var nodes = zTree.getSelectedNodes();
    
    if (nodes.length > 0) {
        var requestData = {
            no: treeNode.no,
            me_code: treeNode.me_code,
        };
        
        try {
            const data = await sendCustomAjaxRequest('POST', '/admin/settings/menuLoader', requestData, false);
            if (data.result === "success" && data.data) {
                var selectNode = data.data;
                if (selectNode.me_cate1) {
                    document.getElementById('me_cate1').value = selectNode.me_cate1;
                    // 방금 만든 change 이벤트를 수동으로 트리거하여 me_cate2의 옵션을 로드
                    var event = new Event('change');
                    document.getElementById('me_cate1').dispatchEvent(event);
                }
                document.getElementById('no').value = selectNode.no;
                document.getElementById('me_code').value = selectNode.me_code;
                document.getElementById('me_name').value = selectNode.me_name;
                document.getElementById('me_link').value = selectNode.me_link;
                // me_cate2 값 설정
                if (selectNode.me_cate2) {
                    setTimeout(function() {
                        document.getElementById('me_cate2').value = selectNode.me_cate2;
                    }, 100);
                }
            }
        } catch (error) {
            console.error("Error:", error.message);
        }
    }
}

async function categoryRemove(event, treeId, treeNode) {
    var requestData = {
        cf_id: treeNode.cf_id,
        me_code: treeNode.me_code,
    };

    try {
        const data = await sendCustomAjaxRequest('POST', '/admin/settings/menuDelete', requestData, false, 'deleteMenu');
    } catch (error) {
        console.error("Error:", error);
    }
}

// 메뉴 업데이트 AJAX CallBack
App.registerCallback('updateMenuTree', function(data) {
    if (data.result === 'success') {
        var updateNodeMenu = data.data;
        var zTree = $.fn.zTree.getZTreeObj("menuTree");
        var selectedNode = zTree.getSelectedNodes()[0];
        if (selectedNode) {
            selectedNode.displayName = updateNodeMenu.me_name + " (" + updateNodeMenu.me_code + ")";
            zTree.updateNode(selectedNode);
        }
    }
});

App.registerCallback('deleteMenu', function(data) {
    console.log(data);
});

App.registerCallback('updateMenuOrder', function(data) {
    console.log(data);
    if (data.result === 'success') {
        document.location.reload();
    } else {
        alert(data.message);
    }
});

async function menuOrder(event, treeId, treeNodes, targetNode, moveType, isCopy) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    var nodes = zTree.transformToArray(zTree.getNodes());

    var menuData = nodes.map(function(node) {
        return {
            type: node.type,
            no: node.no,
            me_code: node.me_code,
            level: node.level
        };
    });

    var requestData = {
        menuData: menuData,
    };

    try {
        const data = await sendCustomAjaxRequest('POST', '/admin/settings/menuOrder', requestData, false, 'updateMenuOrder');
    } catch (error) {
        console.error("Error:", error);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // me_cate1 값이 변경될 때마다 이벤트 핸들러
    const meCate1 = document.getElementById('me_cate1');
    const meCate2 = document.getElementById('me_cate2');
    const meName = document.getElementById('me_name');
    const meLink = document.getElementById('me_link');

    meCate1.addEventListener('change', function () {
        const selectedValue = meCate1.value;

        // me_cate2의 옵션 초기화
        meCate2.innerHTML = '<option value="">메뉴 선택</option>';

        // 선택된 값이 데이터에 존재하고, children이 있는지 확인
        if (menuCategory[selectedValue] && menuCategory[selectedValue].children) {
            const children = menuCategory[selectedValue].children;

            // children을 순회하며 옵션 생성
            children.forEach(function (child) {
                const option = document.createElement('option');
                option.value = child.me_cate2;
                option.textContent = child.me_name;
                option.setAttribute('data-title', child.me_title);
                option.setAttribute('data-link', child.me_link);
                meCate2.appendChild(option);
            });
        }
    });

    meCate2.addEventListener('change', function() {
        // 선택된 옵션 가져오기
        const selectedOption = this.options[this.selectedIndex];

        // 선택된 옵션의 data-name과 data-link 속성 값 가져오기
        const dataName = selectedOption.getAttribute('data-title');
        const dataLink = selectedOption.getAttribute('data-link');

        // me_name과 me_link 요소에 값 설정하기
        document.getElementById('me_name').value = dataName || '';
        document.getElementById('me_link').value = dataLink || '';
    });
});

// 노드 삭제 버튼 표시 여부를 결정하는 함수
// 루트 노드(level 0)를 제외한 모든 노드에 대해 삭제 버튼을 표시
function showRemoveBtn(treeId, treeNode) {
    return treeNode.level > 0;
}

// 노드 이름 변경 버튼 표시 여부를 결정하는 함수
// 모든 노드에 대해 이름 변경 버튼을 숨김
function showRenameBtn(treeId, treeNode) {
    return false;
}

// 노드 드래그 시작 전 호출되는 함수
// 드래그 가능 여부를 확인하고, 가능한 경우 현재 드래그 중인 노드들을 저장
function beforeDrag(treeId, treeNodes) {
    for (var i=0,l=treeNodes.length; i<l; i++) {
        if (treeNodes[i].drag === false) {
            curDragNodes = null;
            return false;
        } else if (treeNodes[i].parentTId && treeNodes[i].getParentNode().childDrag === false) {
            curDragNodes = null;
            return false;
        }
    }
    curDragNodes = treeNodes || new Array();
    return true;
}

// 노드 드롭 전 호출되는 함수
// 대상 노드가 드롭을 허용하는지 확인
function beforeDrop(treeId, treeNodes, targetNode, moveType, isCopy) {
    return targetNode ? targetNode.drop !== false : true;
}

// 노드를 다른 노드 이전에 드롭할 때 호출되는 함수
// 같은 부모 내에서만 이동 가능하도록 제한
function dropPrev(treeId, nodes, targetNode) {
    var is_break = false;
    try {
        var pNode = targetNode.getParentNode();
        if (pNode && pNode.dropInner === false) {
            //return false;
        } else {
            for (var i=0,l=curDragNodes.length; i<l; i++) {
                var curPNode = curDragNodes[i].getParentNode();
                // 다른 부모로 이동 불가
                if (curPNode && curPNode !== targetNode.getParentNode()) {
                    is_break = true;
                    return false;
                }
                // 다른 레벨로 이동 불가
                if (curPNode && curPNode.level !== targetNode.getParentNode().level) {
                    is_break = true;
                    return false;
                }
            }
        }
        //console.log(is_break);
        return true;
    } catch (e) {
    }
}

// 노드를 다른 노드 다음에 드롭할 때 호출되는 함수
// 같은 레벨 내에서만 이동 가능하도록 제한
function dropNext(treeId, nodes, targetNode) {
    try {
        var pNode = targetNode.getParentNode();
        if (pNode && pNode.dropInner === false) {
            return false;
        } else {
            for (var i=0, l=curDragNodes.length; i<l; i++) {
                var curPNode = curDragNodes[i].getParentNode();
                
                // 같은 레벨이 아닌 경우 드롭 불가
                if (curPNode && curPNode.level !== targetNode.getParentNode().level) {
                    return false;
                }
                
                // 같은 부모가 아닌 경우 드롭 불가
                if (curPNode !== targetNode.getParentNode()) {
                    return false;
                }
            }
        }
        return true;
    } catch (e) {
        console.error("Error in dropNext:", e);
        return false;
    } 
}

// 노드를 다른 노드 내부로 드롭할 때 호출되는 함수
function dropInner(treeId, nodes, targetNode) {
    // 모든 내부 드롭을 방지
    return false;
}

// 노드 더블클릭 시 확장/축소 여부를 결정하는 함수
// 루트 노드가 아닌 경우에만 확장/축소 가능
function dblClickExpand(treeId, nodes, targetNode) {
    return treeNode.level > 0;
}

// 노드 이름 편집 전 호출되는 함수
// 항상 편집을 허용
function beforeEditName(treeId, nodes, targetNode) {
    return true; 
}

// 노드 제거 전 호출되는 함수
// 항상 제거를 허용
function beforeRemove(treeId, nodes, targetNode) {
    return true; 
}
</script>
