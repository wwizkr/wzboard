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
                            <option value="<?php echo $_ENV['DB_TABLE_PREFIX'];?>adversting_items.storeName" <?= in_array('storeName', $_GET['filter'] ?? []) ? 'selected' : ''; ?>>스토어명</option>
                            <option value="<?php echo $_ENV['DB_TABLE_PREFIX'];?>adversting_items.sellerId" <?= in_array('sellerId', $_GET['filter'] ?? []) ? 'selected' : ''; ?>>셀러아이디</option>
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
                        <div class="list-col col-custom-120 text-center">스토어명</div>
                        <div class="list-col col-custom-auto text-center">상품명</div>
                        <div class="list-col col-custom-80 text-center">슬롯수</div>
                        <div class="list-col col-custom-80 text-center">기간</div>
                        <div class="list-col col-custom-80 text-center">구분</div>
                        <div class="list-col col-custom-120 text-center">등록일</div>
                    </div>
                </li>
                <?php
                if(!empty($periodList)) {
                    foreach($periodList as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - intval($key);
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'" data-data=\''.json_encode($val).'\'>';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['program']['companyName'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['manager']['nickName'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['seller']['nickName'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">'.$val['programItem'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['storeName'].'</div>';
                                echo '<div class="list-col col-custom-auto text-left">'.$val['itemName'].'</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['slotCnt'].'개</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['period'].'일</div>';
                                echo '<div class="list-col col-custom-80 text-center">'.$val['orderType'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['period_at'].'</div>';
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