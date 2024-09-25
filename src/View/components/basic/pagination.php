<?php
$startPage = max(1, $currentPage - floor($pageNums / 2));
$endPage = min($totalPages, $startPage + $pageNums - 1);

if ($endPage - $startPage + 1 < $pageNums) {
    $startPage = max(1, $endPage - $pageNums + 1);
}

$showFirstLast = $totalPages > 10;
?>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if ($showFirstLast && $currentPage > 1): ?>
            <li class="page-item page-start">
                <a class="page-link" href="?page=1<?= $queryString ?>">&laquo; 처음</a>
            </li>
        <?php endif; ?>

        <?php if ($currentPage > 1): ?>
            <li class="page-item page-prev">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $queryString ?>">이전</a>
            </li>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item page-count <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item page-next">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $queryString ?>">다음</a>
            </li>
        <?php endif; ?>

        <?php if ($showFirstLast && $currentPage < $totalPages): ?>
            <li class="page-item page-end">
                <a class="page-link" href="?page=<?= $totalPages ?><?= $queryString ?>">끝 &raquo;</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>