<link href="/assets/css/board/<?= $boardConfig['board_skin']; ?>/style.css" rel="stylesheet">
<form name="flist" id="flist">
<div class="page-container">
    <div class="table-container">
        <div class="table-list">
            <ul class="list-group">
                <li class="list-group-li list-group-head">
                    <div class="list-group-row">
                        <div class="list-group-col col-custom-60 text-center">번호</div>
                        <div class="list-group-col col-custom-auto text-center">제목</div>
                        <div class="list-group-col text-center">작성자</div>
                        <div class="list-group-col col-custom-80 text-center">조회</div>
                    </div>
                </li>
                <?= $articleHtml; ?>
            </ul>
        </div>
        <div class="table-button table-button-between">
            <div class="table-button-s"></div>
            <div class="table-button-e">
                <ul>
                    <li><a href="/board/<?= $boardConfig['board_id']; ?>/write" class="btn btn-sm btn-primary me-2">글쓰기</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>
</form>