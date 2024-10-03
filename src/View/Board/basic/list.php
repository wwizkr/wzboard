<div class="page-container">
    <h2 class="page-title board-title"><?= $boardConfig['board_name'];?></h2>
    <div class="table-container board-container">
        <div class="board-top">
            <div class="board-top-nav">
                <span class="total">전체 <?= number_format($paginationData['totalItems']); ?>건</span>
                <span class="current">현재 <?= number_format($paginationData['currentPage']); ?>페이지</span>
            </div>
            <div class="board-search">
                <button type="button" class="btn btn-search" onclick="setSearchForm();">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63402 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63402 17.5 10.5 17.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15.5 15.5L20.5 20.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
        <?php if (!empty($categoryData)) { ?>
        <div class="board-category swiper-container">
            <ul class="swiper-wrapper">
                <li class="swiper-slide"><a href="/board/<?= $boardConfig['board_id'];?>/list" class="<?= !isset($_GET['category']) ? 'active' : ''; ?>">전체</a></li>
                <?php foreach($categoryData as $key=>$val) { ?>
                <li class="swiper-slide">
                    <a href="/board/<?= $boardConfig['board_id']; ?>/list?category[]=<?= $val['category_name']; ?>" class="<?= isset($_GET['category']) && in_array($val['category_name'], $_GET['category']) ? 'active' : '';?>"><?= $val['category_name']; ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <script>
        const categorySwiper = new Swiper('.board-category', {
            slidesPerView: 'auto',
            touchRatio: 1,
            observer: true,
            observeParents: true,
        });
        </script>
        <?php } ?>
        <form name="flist" id="flist">
        <div class="table-list-container">
            <ul class="table-list-wrapper board-list">
                <li class="table-list-row list-head">
                    <div class="list-row">
                        <div class="list-col col-custom-60 mobile-none">번호</div>
                        <div class="list-col col-custom-auto">제목</div>
                        <div class="list-col">작성자</div>
                        <div class="list-col col-custom-80">조회</div>
                    </div>
                </li>
                <?= $articleHtml; ?>
            </ul>
        </div>
        <div class="table-button justify-between">
            <div class="table-button-s"></div>
            <div class="table-button-e">
                <ul>
                    <li><a href="/board/<?= $boardConfig['board_id']; ?>/write" class="btn btn-fill-accent">글쓰기</a></li>
                </ul>
            </div>
        </div>
        </form>
    </div>
    <?php if (isset($paginationData)) { echo $this->renderPagination($paginationData); } ?>
</div>


<script>
function setSearchForm() {
    var searchHtml = `
        <form name="fsearch" method="get" action="/board/<?= $boardConfig['board_id']; ?>/list" onsubmit="return fboardSearch(this);">
        <div class="board-search-form">
            <div class="board-search-filter">
                <div class="filter">
                    <input type="checkbox" name="filter[]" id="ft_title" value="title" <?= (!isset($_GET['filter']) || (isset($_GET['filter']) && in_array('title', $_GET['filter']))) ? 'checked' : ''; ?>>
                    <label for="ft_title">제목</label>
                </div>
                <div class="filter">
                    <input type="checkbox" name="filter[]" id="ft_content" value="content" <?= (isset($_GET['filter']) && in_array('content', $_GET['filter'])) ? 'checked' : ''; ?>>
                    <label for="ft_content">내용</label>
                </div>
                <div class="filter">
                    <input type="checkbox" name="filter[]" id="ft_nickname" value="nickName" <?= (isset($_GET['filter']) && in_array('nickName', $_GET['filter'])) ? 'checked' : ''; ?>>
                    <label for="ft_nickname">글쓴이</label>
                </div>
            </div>
            <div class="board-search-input">
                <input type="text" name="search" id="search" value="<?= $_GET['search'] ?? ''; ?>" placeholder="검색어를 입력하세요">
                <button type="submit" class="btn btn-search">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63402 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63402 17.5 10.5 17.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15.5 15.5L20.5 20.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
        </form>
    `;

    modalOpen('boardSearchModal', 'open-board-search', '검색', searchHtml);
}
function fboardSearch(frm) {
    return true
}
</script>