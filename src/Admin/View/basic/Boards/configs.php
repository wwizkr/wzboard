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
                        <div class="col-3 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if(!empty($boardData)) {
                    $num = count($boardData);
                    foreach($boardData as $key=>$val) {
                        echo '<li class="list-group-item list-group-body">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-1 list-group-col text-center">'.$num.'</div>';
                                echo '<div class="col-3 list-group-col text-center">'.$val['group_name'].'</div>';
                                echo '<div class="col-3 list-group-col text-center">'.$val['board_id'].'</div>';
                                echo '<div class="col list-group-col text-center">'.$val['board_name'].'</div>';
                                echo '<div class="col-3 list-group-col list-group-button text-center">관리</div>';
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