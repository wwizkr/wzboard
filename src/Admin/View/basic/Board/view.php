<div class="content-fixed-top">
    <div class="fixed-top-inner">
        <h3 class="page-title"><?php echo $title ? $title : '' ?></h3>
        <div class="fixed-top-btn">
            <a href="/admin/board/<?= $boardsConfig['board_id']; ?>/list" class="btn btn-primary">목록</a>
        </div>
    </div>
</div>
<div class="page-container container-fluid">
    <div class="col-12 mb-3 table-container">
        <div class="p-3 table-form table-content">
            <div class="content-header">
                <p><?= $articleData['title']; ?></p>
            </div>
            <div class="content-body">
                <div class="content">
                <?= $articleData['content']; ?>
                </div>
            </div>
        </div>
        <div class="table-button table-button-between">
            <div class="table-button-s"></div>
            <div class="table-button-e">
                <ul class="d-flex">
                    <li><a href="/admin/board/<?= $boardsConfig['board_id']; ?>/write/<?= $articleData['no']; ?>" class="btn btn-sm btn-primary me-2">글수정</a></li>
                    <li><a href="/admin/board/<?= $boardsConfig['board_id']; ?>/delete/<?= $articleData['no']; ?>" class="btn btn-sm btn-primary me-2">글삭제</a></li>
                    <li><a href="/admin/board/<?= $boardsConfig['board_id']; ?>/list" class="btn btn-sm btn-primary me-2">글목록</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php if($boardsConfig['is_use_comment']) { ?>
    <div class="col-12 mb-3 table-container">
        <div class="comment-container">
            <div id="comment-write-form">
                <form name="frm" id="frm">
                <input type="hidden" name="board_id" id="board_id" value="<?= $boardsConfig['board_id']; ?>">
                <input type="hidden" name="article_no" id="article_no" value="<?= $articleData['no']; ?>">
                <input type="hidden" name="comment_no" id="comment_no" value="">
                <input type="hidden" name="parent_no" id="parent_no" value="">
                <div class="table-form table-comment-form">
                    <div class="table-row row mb-3">
                        <div class="table-td col-12">
                            <textarea name="formData[content]" id="comment_content" class="editor-form" data-toolbar="simple" data-menubar="false" data-height="200"></textarea>
                        </div>
                    </div>
                </div>
                <div class="table-button table-button-between">
                    <div class="table-button-s"></div>
                    <div class="table-button-e">
                        <ul>
                            <li><button type="button" class="btn btn-sm btn-primary btn-form-submit-ajax me-2" data-target="/admin/board/commentWriteUpdate" data-callback="successCommentUpdate">댓글쓰기</button></li>
                        </ul>
                    </div>
                </div>
                </form>
            </div>
            <div id="comment-list-wrap"></div>
            <div id="comment-more">
                <button type="button" id="btn-comment-more" data-page="1" onclick="loadComment();">댓글 더보기</button>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</form>
<?= $editorScript; ?>
<script>
var boardId = '<?= $boardsConfig['board_id']; ?>';
var articleNo = '<?= $articleData['no']; ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadComment(); // 페이지 로딩 시 첫 번째 페이지의 댓글을 불러옵니다.
});

function successCommentUpdate(data) {
    if (data.result === 'success') {
        if (data.action === 'insert') {
            document.getElementById('frm').reset(); // 폼 초기화
            document.getElementById('btn-comment-more').dataset.page = 1; // 페이지 초기화
            document.getElementById('comment-list-wrap').innerHTML = ''; // 기존 댓글 목록 초기화
            loadComment(); // 새로고침하여 댓글 불러오기
        }
        if (data.action === 'modify') {
            console.log(data);
        }
        if (data.action === 'reply') {
            console.log(data);
        }
    } else {
        alert('댓글 등록에 실패했습니다: ' + data.message);
    }
}

function loadCommentTemplate(callback) {
    var isAdmin = window.location.pathname.includes('/admin/');
    var skinName = 'basic'; // 필요 시 변경 가능

    // 댓글 템플릿 로딩을 위한 fetch 요청
    fetch(`/template/getCommentTemplate?isAdmin=${isAdmin}&skinName=${skinName}`)
        .then(response => {
            if (!response.ok) { // HTTP 응답 코드가 정상적인지 확인
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // 텍스트 형태로 반환
        })
        .then(template => callback(template))
        .catch(error => console.error('템플릿 파일 로드 중 오류 발생:', error));
}

// 댓글을 로드하는 함수
function loadComment() {
    var page = document.getElementById('btn-comment-more').dataset.page;
    console.log(page);
    var perPage = 10;

    loadCommentTemplate(function(template) {
        sendCustomAjaxRequest('POST', `/admin/board/comment/${boardId}/${articleNo}`, { page: page, perPage: perPage }, function(responseText) {
            var data = JSON.parse(responseText);

            if (data.result === 'success' && data.data) {
                data.data.forEach(function(comment) {
                    // 템플릿 내용을 댓글 데이터로 대체
                    var commentHtml = template
                        .replace(/{{no}}/g, comment.no)
                        .replace(/{{content}}/g, comment.content)
                        .replace(/{{nickName}}/g, comment.nickName)
                        .replace(/{{created_at}}/g, comment.created_at);

                    // 댓글을 리스트에 추가
                    var div = document.createElement('div');
                        div.innerHTML = commentHtml;
                        document.getElementById('comment-list-wrap').appendChild(div);
                });

                // 다음 페이지를 위해 page 값을 증가시킵니다.
                document.getElementById('btn-comment-more').dataset.page = parseInt(page) + 1;

                // 더 이상 불러올 댓글이 없을 경우 "더보기" 버튼을 숨깁니다.
                if (data.data.length < perPage) {
                    document.getElementById('comment-more').style.display = 'none';
                }
            } else {
                alert('댓글을 불러오는 데 실패했습니다.');
            }
        }, function(errorMessage) {
            alert('댓글을 불러오는 중 오류가 발생했습니다: ' + errorMessage);
        });
    });
}

// 댓글 수정, 삭제, 답글 기능을 처리하는 함수
function commentAction(button) {
    var action = button.dataset.action;
    var commentElement = button.closest('.common-list');
    var commentNo = commentElement.dataset.no;

    if (tinymce.get('comment_content')) {
        // TinyMCE 인스턴스가 있을 때 제거
        tinymce.remove('#comment_content');
    }

    if (action === 'modify') {
        var commentContent = commentElement.querySelector('.comment-content').innerHTML;

        commentElement.after(document.getElementById('comment-write-form'));

        document.getElementById('comment_no').value = commentNo;
        document.getElementById('parent_no').value = '';

        // TinyMCE가 초기화 가능한 경우 초기화하고 내용 설정
        if (typeof initializeTinyMCE === 'function') {
            initializeTinyMCE('#comment_content', 'basic', false, 200);

            setTimeout(function () {
                if (tinymce.get('comment_content')) {
                    tinymce.get('comment_content').setContent(commentContent);
                } else {
                    console.error('TinyMCE 인스턴스가 존재하지 않습니다.');
                    document.getElementById('comment_content').value = commentContent; // 기본 텍스트박스에 내용 설정
                }
            }, 100);
        } else {
            document.getElementById('comment_content').value = commentContent; // 기본 텍스트박스에 내용 설정
        }

    } else if (action === 'replay') {
        commentElement.after(document.getElementById('comment-write-form'));

        document.getElementById('comment_no').value = '';
        document.getElementById('parent_no').value = commentNo;

        // TinyMCE가 초기화 가능한 경우 초기화하고 내용 설정
        if (typeof initializeTinyMCE === 'function') {
            initializeTinyMCE('#comment_content', 'basic', false, 200);
            setTimeout(function () {
                if (tinymce.get('comment_content')) {
                    tinymce.get('comment_content').setContent('');
                } else {
                    console.error('TinyMCE 인스턴스가 존재하지 않습니다.');
                    document.getElementById('comment_content').value = ''; // 기본 텍스트박스에 내용 초기화
                }
            }, 100);
        } else {
            document.getElementById('comment_content').value = ''; // 기본 텍스트박스에 내용 초기화
        }

    } else if (action === 'delete') {
        if (confirm('이 댓글을 삭제하시겠습니까?')) {
            alert('댓글이 삭제되었습니다.');
        }
    }
}
</script>
