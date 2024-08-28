<form name="flist" id="flist">
<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/members/add">회원 등록</a>
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
                        <div class="col-custom-140 list-group-col text-center">회원등급</div>
                        <div class="col-custom-120 list-group-col text-center">회원아이디</div>
                        <div class="col-custom-120 list-group-col text-center">회원명</div>
                        <div class="col-custom-160 list-group-col text-center">회원연락처</div>
                        <div class="col list-group-col text-center">회원이메일</div>
                        <div class="col-custom-100 list-group-col list-group-button text-center">관리</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?php if (isset($paginationData)): ?>
        <?= $this->renderPagination($paginationData) ?>
    <?php endif; ?>
</div>
</form>