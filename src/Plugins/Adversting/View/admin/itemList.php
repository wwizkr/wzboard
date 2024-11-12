<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-search">
        <div class="local-ov local-ov01">
            <div class="local-left">
                <span class="pg-count pg01">
                    <span class="ov-txt">전체</span>
                    <span class="ov-num"><b><?= $totalItems; ?></b> 건</span>
                </span>
            </div>
            <div class="local-auto">
                <div class="frm-input-row">
                    <div class="frm-input wfpx-100">
                        <select name="filter[]" class="frm_input frm_full">
                            <option value="storeName" <?= in_array('storeName', $_GET['filter'] ?? []) ? 'selected' : ''; ?>>스토어명</option>
                            <option value="sellerId"<?= in_array('sellerId', $_GET['filter'] ?? []) ? 'selected' : ''; ?>>셀러아이디</option>
                        </select>
                    </div>
                    <div class="frm-input frm-ml wfpx-100">
                        <input type="text" name="search" id="search" value="<?php echo $_GET['search'] ?? '' ?>" class="frm_input frm_full">
                    </div>
                    <div class="frm-input frm-ml">
                        <input type="submit" class="btn btn-fill-accent" value="검색">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <form name="flist" id="flist">
    <div class="content-fixed-top">
        <div class="fixed-top-inner">
            <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
            <div class="fixed-top-btn">
                <a href="/adversting/admin/itemForm" class="btn btn-fill-accent">광고 상품 등록</a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-list-container">
            <ul class="table-list-wrapper">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-120 text-center">프로그램명</div>
                        <div class="list-col col-custom-120 text-center">등록회원</div>
                        <div class="list-col col-custom-120 text-center">셀러회원</div>
                        <div class="list-col col-custom-100 text-center">광고상품</div>
                        <div class="list-col col-custom-auto text-center">스토어명</div>
                        <div class="list-col col-custom-80 text-center">슬롯수</div>
                        <div class="list-col col-custom-80 text-center">시작순위</div>
                        <div class="list-col col-custom-100 text-center">시작일</div>
                        <div class="list-col col-custom-100 text-center">연장일</div>
                        <div class="list-col col-custom-100 text-center">종료일</div>
                        <div class="list-col col-custom-80 text-center">상태</div>
                        <div class="list-col col-custom-120 text-center">등록일</div>
                        <div class="list-col col-custom-120 text-center">수정일</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($itemList)) {
                    foreach($itemList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'" data-data=\''.json_encode($val).'\'>';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['program']['companyName'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['manager']['nickName'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['seller']['nickName'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.$val['programItem'].'</div>';
                                echo '<div class="list-col col-custom-auto text-center">'.$val['storeName'].'</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['slotCount'].'</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['startRanking'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.substr($val['start_at'], 0, 10).'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.substr($val['extension_at'], 0, 10).'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.substr($val['close_at'], 0, 10).'</div>';
                                echo '<div class="list-col col-custom-80 text-center">';
                                    echo '<select name="status['.$key.']" class="frm_input" data-proto="'.$val['status'].'">';
                                    foreach([1=>'진행',2=>'종료'] as $idx => $status) {
                                        $selected = $val['status'] == $idx ? 'selected' : '';
                                        echo '<option value="'.$idx.'" '.$selected.'>'.$status.'</option>';
                                    }
                                    echo '</select>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.substr($val['created_at'], 0, 16).'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.substr($val['updated_at'], 0, 16).'</div>';
                                echo '<div class="list-col list-col-row col-custom-100 text-center">';
                                    echo '<a href="/adversting/admin/itemForm/'.$val['no'].$queryString.'" class="btn btn-ssm btn-fill-accent">수정</a>';
                                    echo '<a href="javascript:void(0);" onclick="confirmDeleteBefore(this);" data-target="/adversting/admin/itemDelete" data-no="'.$val['no'].'" data-callback="updateItemDelete" class="btn btn-ssm btn-fill-darkgray ml-1">삭제</a>';
                                echo '</div>';
                            echo '</div>';
                            echo '<div class="list-row list-row2">';
                                echo '<div class="list-col col-custom-auto">';
                                    echo '<div class="frm-input-row">';
                                        echo '<div class="frm-input">';
                                            echo '<span class="frm_text">광고집행내역</span>';
                                        echo '</div>';
                                        echo '<div class="frm-guide">'.str_replace("-", "일-", $val['periodHistory']).'일</div>';
                                        echo '<div class="frm-input frm-ml">';
                                            echo '<button type="button" class="btn btn-fill-lightpurple" onclick="viewPeriodHistory(this);">자세히보기</button>';
                                        echo '</div>';
                                        echo '<div class="frm-input wfpx-30">';
                                        echo '</div>';
                                        echo '<div class="frm-input frm-ml">';
                                            echo '<span class="frm_text">현재 순위</span>';
                                        echo '</div>';
                                        echo '<div class="frm-input frm-ml wfpx-50">';
                                            echo '<input type="text" name="updateRanking['.$key.']" value="'.$val['updateRanking'].'" class="update-ranking frm_input frm_full text-center">';
                                        echo '</div>';
                                        echo '<div class="frm-input frm-ml">';
                                            echo '<button type="button" class="btn btn-fill-colorgreen" onclick="processedCheckRanking(this);">순위확인</button>';
                                        echo '</div>';
                                        echo '<div class="frm-input frm-ml">';
                                            echo '<span class="frm_text">순위변동내역</span>';
                                        echo '</div>';
                                        echo '<div class="frm-guide">'.str_replace("-", "위-", $val['rankingHistory']).'위</div>';
                                        echo '<div class="frm-input frm-ml">';
                                            echo '<button type="button" class="btn btn-fill-lightpurple" onclick="viewRankingHistory(this);">자세히보기</button>';
                                        echo '</div>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-auto">';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    </form>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>

<script>
async function viewPeriodHistory(el) {
    const parentRow = el.closest('.table-list-row');
    const itemData = parentRow.dataset.data;
    const item = JSON.parse(itemData);

    var data = {programNo: item.programNo, itemNo: item.no}
    var url = '/adversting/admin/viewPeriodHistory';
    console.log(data);

    try {
        const result = await sendCustomAjaxRequest('POST', url, data, true, 'sucessPeriodHistory')
    }
    catch (error) {
        alert('광고집행 목록 조회에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
    }
}

async function viewRankingHistory(el) {
    const parentRow = el.closest('.table-list-row');
    const itemData = parentRow.dataset.data;
    const item = JSON.parse(itemData);

    var data = {programNo: item.programNo, itemNo: item.no}
    var url = '/adversting/admin/viewRankingHistory';
    console.log(data);

    try {
        const result = await sendCustomAjaxRequest('POST', url, data, true, 'sucessRankingHistory')
    }
    catch (error) {
        alert('순위 목록 조회에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
    }
}

async function processedCheckRanking(el) {
    const parentRow = el.closest('.table-list-row');
    const itemData = parentRow.dataset.data;
    const item = JSON.parse(itemData);
    
    const itemNo = item.no;
    const storeName = item.storeName;
    const itemCode = item.itemCode;
    const matchCode = item.matchCode;
    const searchKeyword = item.searchKeyword;
    const oQuery = item.oQuery;
    const adQuery = item.adQuery;

    var data = {itemNo: itemNo, storeName: storeName, itemCode: itemCode, matchCode: matchCode, searchKeyword: searchKeyword, oQuery: oQuery, adQuery: adQuery};
    var url = '/adversting/admin/searchNaverShopRank/update';

    try {
       const result = await sendCustomAjaxRequest('POST', url, data, true);
       if (result.result === 'success') {
           const updateRankingElement = parentRow.querySelector('.update-ranking');
           updateRankingElement.value = result.data.rank;
           alert('현재 순위는 ' + result.data.rank + '위 입니다. 업데이트 하였습니다.');
           location.reload();
       }
    }
    catch (error) {
        alert('순위 검색에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
    }
}

App.registerCallback('sucessPeriodHistory', function(data) {
    const listData = data.data.list;
    const html = `
        <div class="table-list-container">
            <ul class="table-list-wrapper table-color-lightblue">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-auto text-center">상품명</div>
                        <div class="list-col col-custom-80 text-center">기간</div>
                        <div class="list-col col-custom-80 text-center">구분</div>
                        <div class="list-col col-custom-100 text-center">등록일</div>
                    </div>
                </li>
                ${listData.map((item, index) => `
                    <li class="table-list-row list-body">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">${index + 1}</div>
                            <div class="list-col col-custom-auto text-left">${item.item.itemName}</div>
                            <div class="list-col col-custom-80 text-center">${item.period}</div>
                            <div class="list-col col-custom-80 text-center">${item.orderType}</div>
                            <div class="list-col col-custom-100 text-center">${item.created_at}</div>
                        </div>
                    </li>
                `).join('')}
            </ul>
        </div>
    `;

    modalOpen('period', 'period-history', '광고집행 내역 보기', html);
});

App.registerCallback('sucessRankingHistory', function(data) {
    const listData = data.data.list;
    const html = `
        <div class="table-list-container">
            <ul class="table-list-wrapper table-color-lightblue">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-auto text-center">상품명</div>
                        <div class="list-col col-custom-80 text-center">순위</div>
                        <div class="list-col col-custom-140 text-center">확인일</div>
                    </div>
                </li>
                ${listData.map((item, index) => `
                    <li class="table-list-row list-body">
                        <div class="list-row">
                            <div class="list-col col-custom-60 text-center">${index + 1}</div>
                            <div class="list-col col-custom-auto text-left">${item.item.itemName}</div>
                            <div class="list-col col-custom-80 text-center">${item.ranking}</div>
                            <div class="list-col col-custom-140 text-center">${item.created_at}</div>
                        </div>
                    </li>
                `).join('')}
            </ul>
        </div>
    `;

    modalOpen('ranking', 'ranking-history', '상품 순위 변동 보기', html);
});

App.registerCallback('updateListModify', function(data) {
    //location.reload();
});

App.registerCallback('updateListDelete', function(data) {
    //location.reload();
});

App.registerCallback('updateItemDelete', function(data) {
    //location.reload();
});
</script>