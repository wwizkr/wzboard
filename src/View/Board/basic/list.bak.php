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
<script>
var paginationData = <?= json_encode($paginationData); ?>;
var articleData = <?= json_encode($articleData); ?>;
document.addEventListener('DOMContentLoaded', function() {
    loadArticleList(); // 페이지 로딩이 완료되면 loadArticleList 함수 실행
});

function loadArticleList() {
    // 템플릿 파일을 로드하는 함수
    loadTemplate('getArticleTemplate', function(template) {
        // 기존 리스트에서 첫 번째 <li>를 제외한 나머지를 제거
        var listContainer = document.querySelector('.list-group');
        var listItems = listContainer.querySelectorAll('li.list-group-item:not(:first-child)');

        listItems.forEach(function(item) {
            item.remove(); // 첫 번째 <li>를 제외하고 나머지 <li>를 모두 제거
        });

        // articleData와 paginationData를 사용하여 $num 계산
        articleData.forEach(function(article, index) {
            // $num 계산식: totalItems - (currentPage - 1) * itemsPerPage - index
            var num = paginationData.totalItems - ((paginationData.currentPage - 1) * paginationData.itemsPerPage) - index;

            // 템플릿 파일의 내용을 기사 데이터로 대체
            var articleHtml = template
                .replace(/{{num}}/g, num) // num 값을 사용하여 대체
                .replace(/{{articleNo}}/g, article.no)
                .replace(/{{boardId}}/g, article.board_id)
                .replace(/{{title}}/g, article.title)
                .replace(/{{nickName}}/g, article.nickName)
                .replace(/{{date}}/g, article.created_at);

            // 새 <li> 요소를 생성하고 템플릿 내용을 삽입
            var newListItem = document.createElement('li');
            newListItem.className = 'list-group-item list-group-body';
            newListItem.innerHTML = articleHtml;

            // 첫 번째 <li> 뒤에 새 항목을 추가
            listContainer.appendChild(newListItem);
        });
    });
}
</script>