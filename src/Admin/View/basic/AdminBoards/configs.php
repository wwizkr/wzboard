<form name="flist" id="flist">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/boards/boardform/create">게시판 생성</a>
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
                        <div class="col-1 list-group-col text-center">번호</div>
                        <div class="col-3 list-group-col text-center">그룹명</div>
                        <div class="col-3 list-group-col text-center">게시판 ID</div>
                        <div class="col list-group-col text-center">게시판명</div>
                        <div class="col-custom-220 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($boardsConfig)) {
                    $num = count($boardsConfig);
                    foreach($boardsConfig as $key=>$val) {
                        echo '<li class="list-group-item list-group-body">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-1 list-group-col text-center">'.$num.'</div>';
                                echo '<div class="col-3 list-group-col text-center">'.$val['group_name'].'</div>';
                                echo '<div class="col-3 list-group-col text-center">'.$val['board_id'].'</div>';
                                echo '<div class="col list-group-col text-center">'.$val['board_name'].'</div>';
                                echo '<div class="col-custom-220 list-group-col list-group-button text-center">';
                                    echo '<a href="" class="btn btn-sm btn-info text-white me-2">수정</a>';
                                    echo '<a href="" class="btn btn-sm btn-danger me-2">삭제</a>';
                                    echo '<a href="/admin/board/'.$val['board_id'].'/list" class="btn btn-sm btn-outline-secondary me-2">목록</a>';
                                    echo '<a href="/admin/board/'.$val['board_id'].'/write" class="btn btn-sm btn-primary me-2">글쓰기</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                        $num--;
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
</form>