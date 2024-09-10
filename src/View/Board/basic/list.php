<link href="/assets/css/board/<?= $boardConfig['board_skin']; ?>/style.css" rel="stylesheet">
<div class="page-container">
    <div class="table-container">
        <?php
        /*
         * 체크박스를 이용해서 여러개의 카테고리를 선택할 수 있도록 수정
        */
        if (!empty($categoryData)) {
            echo '<div class="board-category">';
            foreach($categoryData as $key=>$val) {
                echo '<a href="/board/'.$boardConfig['board_id'].'/list?category[]='.$val['category_name'].'">'.$val['category_name'].'</a>';
            }
            echo '</div>';
        }
        ?>
        <form name="frm" id="frm" method="get" action="/board/<?= $boardConfig['board_id']; ?>/list" onsubmit="return fboardSearch(this);">
        <div class="board-search">
            <input type="checkbox" name="filter[]" id="ft_title" value="title" <?= (!isset($_GET['filter']) || (isset($_GET['filter']) && in_array('title', $_GET['filter']))) ? 'checked' : ''; ?>>
            <label for="ft_title">제목</label>
            <input type="checkbox" name="filter[]" id="ft_content" value="content" <?= (isset($_GET['filter']) && in_array('content', $_GET['filter'])) ? 'checked' : ''; ?>>
            <label for="ft_content">내용</label>
            <input type="checkbox" name="filter[]" id="ft_nickname" value="nickName" <?= (isset($_GET['filter']) && in_array('nickName', $_GET['filter'])) ? 'checked' : ''; ?>>
            <label for="ft_nickname">글쓴이</label>

            <input type="text" name="search" id="search" value="<?= $_GET['search'] ?? ''; ?>" class="form-control" style="max-width:200px;">
            <button type="submit" class="btn btn-primary">검색</button>
        </div>
        </form>
        <form name="flist" id="flist">
        <div class="table-list board-list">
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
        </form>
    </div>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>
<script>
function fboardSearch(frm) {
    return true
}
</script>