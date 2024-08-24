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
                <input type="hidden" name="action" value="" id="action">
                <input type="hidden" name="group_no" value="" id="group_no">
                <div class="p-3 table-form table-form-md">
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="group_id" class="form-label">그룹아이디</label>
                        </div>
                        <div class="table-td col-md-8">
                            <input type="text" name="formData[group_id]" value="" class="form-control" id="group_id">
                        </div>
                    </div>
                    <div class="table-row row mb-3">
                        <div class="table-th col-md-4">
                            <label for="group_name" class="form-label">그룹명</label>
                        </div>
                        <div class="table-td col-md-8">
                            <input type="text" name="formData[group_name]" value="" class="form-control" id="group_name">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-form-submit-ajax" data-target="/admin/settings/menuUpdate">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/jquery-3.7.1.min.js"></script>
<script src="/assets/js/jquery-migrate-3.5.0.min.js"></script>
<script src="/assets/js/lib/ztree/js/jquery.ztree.all.min.js"></script>
<script>
var treeId = "menuTree";

// 기본 root 노드 설정
var zNodes = <?php echo json_encode($menuDatas); ?>;
if (zNodes.length === 0) {
    zNodes = [{
        name: "Root",
        code: 0,
        meid: 0,
        parent: 0,
        depth: 0,
        open: true,
        type: "root",
        isParent: true
    }];
}

$(document).ready(function(){
    $.fn.zTree.init($("#" + treeId), getTreeSetting(), zNodes);

    $('#add_depth1').on('click', function() {
        var zTree = $.fn.zTree.getZTreeObj(treeId);
        var rootNode = zTree.getNodeByParam('code', 0);
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
                enable: false
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
        console.log(treeNode.type);
        $.ajax({
            type: "POST",
            url: "/admin/settings/menuInsert",
            data: {
                act: "insert",
                type: treeNode.type,
                me_name: newNodeName,
                me_code: treeNode.code,
                me_parent: treeNode.parent,
                me_depth: treeNode.depth
            },
            success: function(data) {
                console.log(data);
                /*
                if (data.result === "success") {
                    var newNode = data.data;
                    var addedNode = zTree.addNodes(treeNode, {
                        name: newNode.name,
                        isParent: newNode.isParent,
                        open: newNode.open,
                        type: "sub",
                        meid: newNode.me_id,
                        code: newNode.me_code,
                        depth: newNode.me_depth,
                        parent: newNode.me_parent
                    });
                    zTree.selectNode(addedNode[0]);
                }
                */
            }
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
            me_id: node.meid,
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
        $.ajax({
            type: "POST",
            url: "/admin/settings/menuLoader",
            data: {
                act: "loader",
                me_id: treeNode.meid,
                code: treeNode.code,
                depth: treeNode.depth,
                parent: treeNode.parent
            },
            success: function(response) {
                if (response.result === "success") {
                    loadMenuForm(response.data);
                }
            }
        });
    }
}

function loadMenuForm(menuData) {
    $("#me_id").val(menuData.me_id);
    $("#me_code").val(menuData.me_code);
    $("#me_type").val(menuData.me_type).prop("selected", true);
    $("#me_name").val(menuData.me_name);
    $("#me_link").val(menuData.me_link);
    $("#me_target").val(menuData.me_target || "self").prop("selected", true);
    $("#me_fcolor").val(menuData.me_fcolor);
    $("#me_fsize").val(menuData.me_fsize);
    $("#me_fweight").prop("checked", menuData.me_fweight == 1);
    $("#me_class").val(menuData.me_class);
    $("#me_pc_use").val(menuData.me_pc_use).prop("selected", true);
    $("#me_mo_use").val(menuData.me_mo_use).prop("selected", true);
    $("#me_pa_use").val(menuData.me_pa_use).prop("selected", true);
}

function menuUpdate(form) {
    var $form = $(form);
    var zTree = $.fn.zTree.getZTreeObj(treeId);
    var nodes = zTree.getSelectedNodes();

    if (!$("#me_code").val()) {
        alert("메뉴 코드를 입력하세요.");
        return false;
    }
    if (!$("#me_name").val()) {
        alert("메뉴명을 입력하세요.");
        return false;
    }
    if (!$("#me_link").val()) {
        alert("연결 주소를 입력하세요.");
        return false;
    }

    var formData = new FormData($form[0]);

    $.ajax({
        type: "POST",
        url: "/admin/settings/menuUpdate",
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.result === "success") {
                nodes[0].name = data.data.name;
                zTree.updateNode(nodes[0]);
                alert("수정되었습니다.");
            } else if (data.message) {
                alert(data.message);
            }
        }
    });

    return false;
}

function categoryRename(event, treeId, treeNode) {
    $.ajax({
        type: "POST",
        url: "/admin/settings/menuRename",
        data: {
            act: "rename",
            name: treeNode.name,
            type: treeNode.type,
            me_id: treeNode.meid
        },
        success: function(response) {
            console.log(response);
        }
    });
}

function categoryRemove(event, treeId, treeNode) {
    $.ajax({
        type: "POST",
        url: "/admin/settings/menuDelete",
        data: {
            act: "delete",
            type: treeNode.type,
            me_id: treeNode.meid
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
