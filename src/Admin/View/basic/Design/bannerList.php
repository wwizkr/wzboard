<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/banner/bannerForm">배너 등록</a>
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
                        <div class="col-custom-140 list-group-col text-center">ID</div>
                        <div class="col-custom-160 list-group-col text-center">출력위치</div>
                        <div class="col-custom-80 list-group-col text-center">순서</div>
                        <div class="col-custom-80 list-group-col text-center">칸수</div>
                        <div class="col-custom-260 list-group-col text-center">관리용제목</div>
                        <div class="col list-group-col text-center">출력아이템</div>
                        <div class="col-custom-120 list-group-col text-center">사용</div>
                        <div class="col-custom-100 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
                <?php
                if (!empty($listData)) {
                    foreach($listData as $key=>$val) {
                        echo '<li class="list-group-item list-group-body">';
                            echo '<div class="row list-group-row">';
                                echo '<div class="col-custom-60 list-group-col text-center">번호</div>';
                                echo '<div class="col-custom-140 list-group-col text-center">ID</div>';
                                echo '<div class="col-custom-160 list-group-col text-center">출력위치</div>';
                                echo '<div class="col-custom-80 list-group-col text-center">순서</div>';
                                echo '<div class="col-custom-80 list-group-col text-center">칸수</div>';
                                echo '<div class="col-custom-260 list-group-col text-center">관리용제목</div>';
                                echo '<div class="col list-group-col text-center">출력아이템</div>';
                                echo '<div class="col-custom-120 list-group-col text-center">사용</div>';
                                echo '<div class="col-custom-100 list-group-col list-group-button text-center">';
                                    echo '<a href="/admin/template/templateForm?ct_id='.$val['ct_id'].'">수정</a>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>