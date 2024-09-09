<link href="/assets/css/board/<?= $boardConfig['board_skin']; ?>/style.css" rel="stylesheet">
<form name="flist" id="flist">
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <h2>목록</h2>
        <div class="p-3 table-list table-list-scroll">
            <ul class="list-group">
                <li class="list-group-item list-group-head">
                    <div class="row list-group-row">
                        <div class="col-custom-60 list-group-col text-center">번호</div>
                        <div class="col list-group-col text-center">제목</div>
                        <div class="col-custom-120 list-group-col text-center">글쓴이</div>
                        <div class="col-custom-120 list-group-col text-center">일자</div>
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