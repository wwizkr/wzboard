<div class="page-container list-container">
    <form name="fsearch" id="fsearch" class="local-sch01 local-sch" method="get">
    <div class="local-ov local-ov01">
        <div class="local-left">
            <span class="pg-count pg01">
                <span class="ov-txt">전체</span>
                <span class="ov-num"><b><?php echo number_format($paginationData['totalItems']); ?></b> 건</span>
            </span>
        </div>
        <div class="local-auto">
            <div class="frm-input-row">
                <div class="frm-input wfpx-100">
                    <input type="text" name="search" id="search" value="<?php echo $_GET['search'] ?? '' ?>" class="frm_input frm_full">
                </div>
                <div class="frm-input frm-ml">
                    <input type="submit" class="btn btn-fill-accent" value="검색">
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
                        <div class="list-col col-custom-60 text-center">선택</div>
                        <div class="list-col col-custom-60 text-center">번호</div>
                        <div class="list-col col-custom-auto text-center">제목</div>
                        <div class="list-col col-custom-120 text-center">글쓴이</div>
                        <div class="list-col col-custom-120 text-center">일자</div>
                        <div class="list-col col-custom-100 text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($articleData)) {
                    foreach($articleData as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - $key;
                        echo '<li class="table-list-row list-body" data-bunch="'.$key.'">';
                            echo '<div class="list-row">';
                                echo '<div class="list-col col-custom-60 text-center">';
                                    echo '<input type="checkbox" name="itemNo['.$key.']" value="'.$val['no'].'" id="check_'.$key.'" class="list-check">';
                                    echo '<label for="check_'.$key.'" class="sound-only">선택</label>';
                                echo '</div>';
                                echo '<div class="list-col col-custom-60 text-center">'.$num.'</div>';
                                echo '<div class="list-col col-custom-auto text-left"><a href="/admin/board/'.$boardConfig['board_id'].'/view/'.$val['no'].'">'.$val['title'].'</a></div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['nickName'].'</div>';
                                echo '<div class="list-col col-custom-120 text-center">'.$val['created_at'].'</div>';
                                echo '<div class="list-col col-custom-100 text-center">관리</div>';
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
