    </div>
</div><!---END container-fluid--->
<footer>
    <p>&copy; <?php echo date('Y'); ?> 회사명. All rights reserved.</p>
</footer>
<script>
document.getElementById('sidebarToggle').addEventListener('click', function () {
    var sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // 현재 URL을 가져옵니다.
    var currentUrl = window.location.pathname;

    // 모든 사이드바 링크를 확인합니다.
    document.querySelectorAll('#sidebar .nav-link').forEach(function(element) {
        var parentLi = element.parentElement;
        var submenu = parentLi.querySelector('.collapse');

        // 현재 URL이 서브메뉴 항목과 일치하면 서브메뉴를 엽니다.
        if (submenu) {
            var links = submenu.querySelectorAll('a');
            links.forEach(function(link) {
                if (link.getAttribute('href') === currentUrl) {
                    submenu.classList.add('show'); // 서브메뉴를 열립니다.
                }
            });
        }

        // 1단계 메뉴를 클릭할 때의 동작을 처리합니다.
        if (element.getAttribute('data-bs-toggle') === 'collapse') {
            element.addEventListener('click', function(e) {
                e.preventDefault(); // 기본 링크 동작을 방지합니다.

                var target = document.querySelector(this.nextElementSibling.getAttribute('id') ? '#' + this.nextElementSibling.getAttribute('id') : '');
                if (target) {
                    var isOpen = target.classList.contains('show');
                    // 모든 다른 서브메뉴를 닫습니다.
                    document.querySelectorAll('#sidebar .collapse.show').forEach(function(openMenu) {
                        openMenu.classList.remove('show');
                    });

                    // 현재 서브메뉴만 토글합니다.
                    if (!isOpen) {
                        target.classList.add('show');
                    }
                }
            });
        }
    });
});
</script>