<link rel="stylesheet" href="/assets/js/lib/ztree/css/metroStyle/metroStyle.css" />

<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="row">
        <!-- 메뉴 트리 영역 -->
        <div class="col-12 col-md-6 order-2 order-md-1 mb-3 mb-md-0 table-container">
            <h2>목록</h2>
            <div class="p-3 table-list table-list-md table-roll">
                <ul id="menuTree" class="ztree"></ul>
            </div>
            <div class="dtable-button-wrap button-right">
                <button type="button" class="btn btn_submit" id="add_depth1">
                    <i class="fa fa-fw fa-lg fa-folder" aria-hidden="true"></i> 1단계 메뉴 추가
                </button>
                <button type="button" class="btn btn_append d-none" id="add_sub">
                    <i class="fa fa-fw fa-lg fa-star" aria-hidden="true"></i> 하위 메뉴 추가
                </button>
            </div>
        </div>

        <!-- 입력폼 영역 -->
        <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0 table-container">
            <h2>입력폼</h2>
            <form name="frm" id="frm">
                <input type="hidden" name="no" value="" id="no">
                <input type="hidden" name="me_code" value="" id="me_code">
                <div class="p-3 table-form table-form-md">
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="me_cate1" class="form-label">그룹아이디</label>
                        </div>
                        <div class="table-td col-md-8">
                            <select name="formData[me_cate1]" id="me_cate1" class="form-select">
                                <option value="">메뉴 분류 선택</option>
                                <option value="boards">게시판</option>
                                <option value="section">페이지</option>
                                <option value="direct">직접입력</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="me_cate2" class="form-label">메뉴 선택</label>
                        </div>
                        <div class="table-td col-md-8">
                            <select name="formData[me_cate2]" id="me_cate2" class="form-select">
                                <option value="">메뉴 선택</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="me_name" class="form-label">메뉴명</label>
                        </div>
                        <div class="table-td col-md-8">
                            <input type="text" name="formData[me_name]" id="me_name" class="form-control require" value="" data-type="text" data-message="메뉴명은 필수입니다.">
                        </div>
                    </div>
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="me_link" class="form-label">메뉴 연결주소</label>
                        </div>
                        <div class="table-td col-md-8">
                            <input type="text" name="formData[me_link]" id="me_link" class="form-control require" value="" data-type="text" data-message="연결주소는 필수입니다.">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/settings/menuUpdate" data-callback="updateMenuTree">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/jquery-3.7.1.min.js"></script>
<script src="/assets/js/jquery-migrate-3.5.0.min.js"></script>
<script src="/assets/js/lib/ztree/js/jquery.ztree.all.min.js"></script>
<script>
function addDisplayNameToNodes(nodes) {
    return nodes.map(function(node) {
        let prefix = '';

        // 하위 메뉴일 경우, depth에 따라 접두사를 추가
        if (node.me_depth > 1) {
            prefix = '- '.repeat(node.me_depth - 1); // depth에 따라 "-"를 반복
        }

        // 현재 노드에 displayName 추가
        node.displayName = prefix + node.me_name + " (" + node.me_code + ")";

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
            onRename: categoryRename,
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
    if (btn) btn.bind("click", function(){
        var zTree = $.fn.zTree.getZTreeObj(treeId);
        
        var newNodeName = treeNode.level === 0 ? "1단계 카테고리명" : "하위카테고리명";

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

        sendCustomAjaxRequest('POST', '/admin/settings/menuInsert', requestData, function(responseText) {
            var data = JSON.parse(responseText);

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
                console.log('노드생성');
                console.log(addedNode[0]);
                zTree.selectNode(addedNode[0]);
                categoryLoader(null, treeId, addedNode[0]);
            } else {
                console.error("Failed to add menu node:", data.message);
            }
            
        }, function(errorMessage) {
            console.error("Error:", errorMessage);
        });
    });
}

function removeHoverDom(treeId, treeNode) {
    $("#addBtn_" + treeNode.tId).unbind().remove();
}

function showRemoveBtn(treeId, treeNode) {
    return treeNode.level > 0;
}

function showRenameBtn(treeId, treeNode) {
    return false;
}

function beforeDrag(treeId, treeNodes) {
    for (var i = 0; i < treeNodes.length; i++) {
        if (treeNodes[i].drag === false) return false;
    }
    return true;
}

function beforeDrop(treeId, treeNodes, targetNode, moveType, isCopy) {
    return targetNode ? targetNode.drop !== false : true;
}

function menuOrder(event, treeId, treeNodes, targetNode, moveType, isCopy) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    var nodes = zTree.transformToArray(zTree.getNodes());

    var menuData = nodes.map(function(node) {
        return {
            type: node.type,
            no: node.no,
            me_code: node.code,
            me_parent: node.parent,
            me_depth: node.depth,
            level: node.level
        };
    });

    $.ajax({
        type: "POST",
        url: "/admin/settings/menuOrder",
        data: { act: "menuorder", menu: menuData },
        success: function(response) {
            console.log(response);
        }
    });
}

function categoryLoader(event, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    var nodes = zTree.getSelectedNodes();
    
    if (nodes.length > 0) {
        var requestData = {
            no: treeNode.no,
            me_code: treeNode.me_code,
        };
        sendCustomAjaxRequest('POST', '/admin/settings/menuLoader', requestData, function(responseText) {
            var data = JSON.parse(responseText);
            if (data.result === "success" && data.data) {
                var selectNode = data.data;
                document.getElementById('no').value = selectNode.no;
                document.getElementById('me_code').value = selectNode.me_code;
                document.getElementById('me_name').value = selectNode.me_name;
                document.getElementById('me_link').value = selectNode.me_link;
            }
        }, function(errorMessage) {
            console.error("Error:", errorMessage);
        });
    }
}

function categoryRemove(event, treeId, treeNode) {
    $.ajax({
        type: "POST",
        url: "/admin/settings/menuDelete",
        data: {
            act: "delete",
            type: treeNode.type,
            me_id: treeNode.no
        },
        success: function(response) {
            if (response.result === "success") {
                var zTree = $.fn.zTree.getZTreeObj(treeId);
                zTree.updateNode(treeNode.getParentNode());
            }
        }
    });
}

function dropPrev(treeId, nodes, targetNode) {
    return true; 
}

function dropNext(treeId, nodes, targetNode) {
    return true; 
}

function dropInner(treeId, nodes, targetNode) {
    return true; 
}

function dblClickExpand(treeId, nodes, targetNode) {
    return true; 
}

function beforeEditName(treeId, nodes, targetNode) {
    return true; 
}

function beforeRemove(treeId, nodes, targetNode) {
    return true; 
}
</script>
