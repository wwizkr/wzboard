<form name="flist" id="flist">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <h2>목록</h2>
        <div class="p-3 table-list table-list table-list-scroll">
            <ul class="list-group">
                <li class="list-group-item list-group-head">
                    <div class="row list-group-row">
                        <div class="col-custom-60 list-group-col text-center">번호</div>
                        <div class="col list-group-col text-center">제목</div>
                        <div class="col-custom-120 list-group-col text-center">글쓴이</div>
                        <div class="col-custom-120 list-group-col text-center">일자</div>
                        <div class="col-custom-100 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($articleData)) {
                    foreach($articleData as $key=>$val) {
                        $num  = $paginationData['totalItems'] - ($paginationData['currentPage'] - 1) * $paginationData['itemsPerPage'] - $key;
                        echo '<li class="list-group-item list-group-body">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-custom-60 list-group-col text-center">'.$num.'</div>';
                                echo '<div class="col list-group-col">'.$val['title'].'</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">'.$val['nickName'].'</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">'.$val['created_at'].'</div>';
                                echo '<div class="col-custom-100 list-group-col list-group-button text-center">관리</div>';
                            echo '</div>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>
</form>