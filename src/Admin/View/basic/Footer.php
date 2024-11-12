    </div>
</div><!---END container-fluid--->
<footer>
    <p>&copy; <?php echo date('Y'); ?> 회사명. All rights reserved.</p>
</footer>
<script>
const activeCode = '<?= $activeCode ?? ''; ?>';
document.getElementById('sidebarToggle').addEventListener('click', function () {
    var sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    //------------------------------------------------------------//
    /*
     * 필수 함수. activeCode 는 메뉴코드를 의미하는 필수 변수
     */
    // 모든 form 요소를 찾습니다.
    document.querySelectorAll('form').forEach(function(form) {
        // 새로운 hidden input 요소를 생성합니다.
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'activeCode';
        hiddenInput.value = activeCode;

        // form에 hidden input을 추가합니다.
        form.appendChild(hiddenInput);
    });

    // 모든 a 요소를 찾습니다.
    document.querySelectorAll('a').forEach(function(link) {
        // href 속성을 가져옵니다.
        let href = link.getAttribute('href');
        
        // href가 있을 때만 처리
        if (href) {
            // URL 객체를 생성합니다.
            const url = new URL(href, window.location.origin);

            // activeCode 파라미터가 없는 경우 추가합니다.
            if (!url.searchParams.has('activeCode')) {
                url.searchParams.append('activeCode', activeCode);
                link.setAttribute('href', url.toString());
            }
        }
    });
    //------------------------------------------------------------//

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

    // 입력폼 탭 nav
    var navbar = document.querySelector('.navbar');
    var navTabs = document.querySelector('.nav-tabs');
    if(navTabs) {
        var fixTop = document.querySelector('.content-fixed-top');
        var stickyOffset = navTabs.offsetTop;
        if (fixTop) {
            var fixTopHeight = fixTop.offsetHeight;
        } else {
            var fixTopHeight = 0;
        }

        function updateTabPosition() {
            var navbarHeight = navbar.offsetHeight;
            navTabs.style.top = (navbarHeight + fixTopHeight) + 'px';
        }

        // 처음 로드 시 위치 설정
        updateTabPosition();

        // 창 크기가 변경될 때마다 위치 업데이트
        window.addEventListener('resize', updateTabPosition);

        // 탭이 스크롤에 따라 고정되도록 처리
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > stickyOffset - navbar.offsetHeight - fixTop.offsetHeight) {
                navTabs.classList.add('sticky-tabs');
            } else {
                navTabs.classList.remove('sticky-tabs');
            }
        });

        // 탭 클릭 시 스크롤 조정
        var navLinks = document.querySelectorAll('.nav-tabs .nav-link');
        if (navLinks.length > 0) {
            navLinks.forEach(function(tab) {
                tab.addEventListener('click', function(event) {
                    event.preventDefault();
                    var target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - navbar.offsetHeight - navTabs.offsetHeight - fixTop.offsetHeight, // navbar와 tab의 높이를 고려하여 스크롤
                            behavior: 'smooth'
                        });
                    }
                });
            });
        }
    }
});
</script>