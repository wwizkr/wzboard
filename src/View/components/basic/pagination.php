<!-- components/skinname/pagination.php -->
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= !empty($queryString) ? $queryString : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<a href="https://search.shopping.naver.com/search/all?query='나이키 신발'" class="boxbs2" target="_blank">네이버 검색어 테스트</a><BR>

<?= '생성된 쿼리스트링::'.$queryString.'<br>'; ?>
<?= '디코딩된 쿼리스트링::'.urldecode($queryString); ?>