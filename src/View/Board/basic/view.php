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
            <div class="content-foot">
                <?php if ($boardConfig['is_use_like'] || $boardConfig['is_use_dislike']) { ?>
                <div class="content-reaction-zone">
                    <?php if ($boardConfig['is_use_like']) { ?>
                    <div class="reaction-zone use-like">
                        <button type="button" class="btn btn-reaction like" data-table="articles" data-action="like">
                            <b>좋아요</b>
                            <span class="reaction-count"><?= $articleData['like_count']; ?></span>
                        </button>
                    </div>
                    <?php } ?>
                    <?php if ($boardConfig['is_use_dislike']) { ?>
                    <div class="reaction-zone use-dislike">
                        <button type="button" class="btn btn-reaction dislike" data-table="articles" data-action="dislike">
                            <b>싫어요</b>
                            <span class="reaction-count"><?= $articleData['dislike_count']; ?></span>
                        </button>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="table-button table-button-between">
            <div class="table-button-s"></div>
            <div class="table-button-e">
                <ul class="d-flex">
                    <li><a href="/board/<?= $boardConfig['board_id']; ?>/write/<?= $articleData['no']; ?>" class="btn btn-sm btn-primary me-2">글수정</a></li>
                    <li><a href="javascript:void(0);" class="btn btn-sm btn-primary me-2" data-target="/board/<?= $boardConfig['board_id']; ?>/delete/<?= $articleData['no']; ?>" data-callback="articleDeleteAfter" data-message="해당 게시물을 삭제하시겠습니까?" onclick="confirmDeleteBefore(this);">글삭제</a></li>
                    <li><a href="/board/<?= $boardConfig['board_id']; ?>/list" class="btn btn-sm btn-primary me-2">글목록</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php if($boardConfig['is_use_comment']) { ?>
    <div class="col-12 mb-3 table-container">
        <div class="comment-container">
            <div id="comment-write-form">
                <form name="frm" id="frm">
                <input type="hidden" name="board_id" id="board_id" value="<?= $boardConfig['board_id']; ?>">
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
                            <li><button type="button" class="btn btn-sm btn-primary btn-form-submit-ajax me-2" data-target="/board/commentWriteUpdate" data-callback="successCommentUpdate">댓글쓰기</button></li>
                        </ul>
                    </div>
                </div>
                </form>
            </div>
            <div id="comment-list-wrap"></div>
            <div id="comment-more">
                <button type="button" id="btn-comment-more">댓글 더보기</button>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</form>
<?= $editorScript; ?>
<script>
var boardId = '<?= $boardConfig['board_id']; ?>';
var articleNo = '<?= $articleData['no']; ?>';
var commentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadComment(commentPage) // 페이지 로딩 시 첫 번째 페이지의 댓글을 불러옵니다.
        .then(() => {
            // 댓글 로딩이 완료된 후 좋아요 버튼에 이벤트 리스너 추가
            var likeButtons = document.querySelectorAll('.btn-reaction');
            likeButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    processedLikeAction(btn);
                });
            });
        })
        .catch(error => {
            console.error('댓글 로딩 중 오류 발생:', error);
        });

    // 더보기 버튼에 클릭 이벤트 리스너 추가
    document.getElementById('btn-comment-more').addEventListener('click', function() {
        commentPage++;
        loadComment(commentPage);
    });
});

App.registerCallback('articleDeleteAfter', function(data) {
});

App.registerCallback('successCommentUpdate', function(data) {
    if (data.result === 'success') {
        if (data.action === 'insert') {
            addNewCommentToTop(data.comment);
        } else if (data.action === 'modify') {
            updateModifiedComment(data.comment);
        } else if (data.action === 'reply') {
            addReplyComment(data.comment);
        }
        resetCommentForm();
    } else {
        alert('댓글 작업에 실패했습니다: ' + data.message);
    }
});

function resetCommentForm(commentNo, parentNo, content) {
    var commentForm = document.getElementById('comment-write-form');
    var commentListWrap = document.getElementById('comment-list-wrap');
    commentListWrap.parentNode.insertBefore(commentForm, commentListWrap);

    document.getElementById('comment_no').value = '';
    document.getElementById('parent_no').value = '';

    if (tinymce.get('comment_content')) {
        // TinyMCE 인스턴스가 있을 때 제거
        tinymce.remove('#comment_content');
    }

    // TinyMCE가 초기화 가능한 경우 초기화하고 내용 설정
    if (typeof initializeTinyMCE === 'function') {
        var commentTextarea = document.getElementById('comment_content');
        var toolbar = commentTextarea.getAttribute('data-toolbar') || 'simple';
        var menubar = commentTextarea.getAttribute('data-menubar') === 'true';
        var height = parseInt(commentTextarea.getAttribute('data-height'), 10) || 200;
        
        initializeTinyMCE('#comment_content', toolbar, menubar, height);

        setTimeout(function () {
            if (tinymce.get('comment_content')) {
                tinymce.get('comment_content').setContent('');
            } else {
                console.error('TinyMCE 인스턴스가 존재하지 않습니다.');
                document.getElementById('comment_content').value = '';
            }
        }, 100);
    } else {
        document.getElementById('comment_content').value = '';
    }
}

function addNewCommentToTop(comment) {
    loadTemplate('getCommentTemplate')
        .then(template => {
            var commentHtml = createCommentHtml(template, comment);
            var div = document.createElement('div');
            div.innerHTML = commentHtml;
            var commentListWrap = document.getElementById('comment-list-wrap');
            commentListWrap.insertBefore(div, commentListWrap.firstChild);
        });
}

function updateModifiedComment(comment) {
    var commentElement = document.querySelector(`[data-comment-id="${comment.no}"]`);
    if (commentElement) {
        loadTemplate('getCommentTemplate')
            .then(template => {
                var commentHtml = createCommentHtml(template, comment);
                commentElement.innerHTML = commentHtml;
            });
    }
}

function addReplyComment(comment) {
    loadTemplate('getCommentTemplate')
        .then(template => {
            var commentHtml = createCommentHtml(template, comment);
            var div = document.createElement('div');
            div.innerHTML = commentHtml;
            var parentComment = document.querySelector(`[data-comment-id="${comment.parent_no}"]`);
            if (parentComment) {
                parentComment.parentNode.insertBefore(div, parentComment.nextSibling);
            } else {
                // 부모 댓글을 찾지 못한 경우, 목록의 맨 위에 추가
                var commentListWrap = document.getElementById('comment-list-wrap');
                commentListWrap.insertBefore(div, commentListWrap.firstChild);
            }
        });
}

function createCommentHtml(template, comment) {
    if (!comment || typeof comment !== 'object') {
        //console.error('Invalid comment data:', comment);
        return '';
    }
    return template
        .replace(/{{no}}/g, comment.no)
        .replace(/{{content}}/g, comment.content)
        .replace(/{{nickName}}/g, comment.nickName)
        .replace(/{{likeCount}}/g, comment.like_count)
        .replace(/{{dislikeCount}}/g, comment.dislike_count)
        .replace(/{{date}}/g, comment.date1);
}

// 댓글을 로드하는 함수
function loadComment(page) {
    return new Promise((resolve, reject) => {
        var perPage = 10;
        loadTemplate('getCommentTemplate')
        .then(template => {
            return sendCustomAjaxRequest('POST', `/board/comment/${boardId}/${articleNo}`, { page: page, perPage: perPage }, false)
                .then(data => {
                    if (data.result === 'success' && data.data && data.data.length > 0) {
                        var commentListWrap = document.getElementById('comment-list-wrap');
                        var fragment = document.createDocumentFragment();

                        data.data.forEach(function(comment) {
                            var commentHtml = createCommentHtml(template, comment);
                            var div = document.createElement('div');
                            div.innerHTML = commentHtml;
                            div.firstChild.setAttribute('data-comment-id', comment.no);
                            fragment.appendChild(div.firstChild);
                        });

                        if (page === 1) {
                            commentListWrap.innerHTML = '';
                        }
                        commentListWrap.appendChild(fragment);

                        document.getElementById('btn-comment-more').style.display = data.data.length < perPage ? 'none' : 'block';
                    } else {
                        if (page === 1) {
                            document.getElementById('comment-list-wrap').innerHTML = '<p class="comment-empty">댓글이 없습니다.</p>';
                        }
                        document.getElementById('btn-comment-more').style.display = 'none';
                    }
                    resolve();
                });
        })
        .catch(error => {
            //console.error('댓글을 불러오는 중 오류가 발생했습니다:', error);
            reject(error);
        });
    });
}

// 댓글 수정, 삭제, 답글 기능을 처리하는 함수
function commentAction(button) {
    var action = button.dataset.action;
    var commentElement = button.closest('.common-list');
    var commentNo = commentElement.dataset.no;

    if (tinymce.get('comment_content')) {
        tinymce.remove('#comment_content');
    }

    var commentForm = document.getElementById('comment-write-form');
    commentElement.after(commentForm);

    if (action === 'modify') {
        var commentContent = commentElement.querySelector('.comment-content').innerHTML;
        document.getElementById('comment_no').value = commentNo;
        document.getElementById('parent_no').value = '';

        // TinyMCE가 초기화 가능한 경우 초기화하고 내용 설정
        if (typeof initializeTinyMCE === 'function') {
            var commentTextarea = document.getElementById('comment_content');
            var toolbar = commentTextarea.getAttribute('data-toolbar') || 'simple';
            var menubar = commentTextarea.getAttribute('data-menubar') === 'true';
            var height = parseInt(commentTextarea.getAttribute('data-height'), 10) || 200;
            
            initializeTinyMCE('#comment_content', toolbar, menubar, height);

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
        document.getElementById('comment_no').value = '';
        document.getElementById('parent_no').value = commentNo;

        // TinyMCE가 초기화 가능한 경우 초기화하고 내용 설정
        if (typeof initializeTinyMCE === 'function') {
            var commentTextarea = document.getElementById('comment_content');
            var toolbar = commentTextarea.getAttribute('data-toolbar') || 'simple';
            var menubar = commentTextarea.getAttribute('data-menubar') === 'true';
            var height = parseInt(commentTextarea.getAttribute('data-height'), 10) || 200;

            initializeTinyMCE('#comment_content', toolbar, menubar, height);
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
            // 여기에 댓글 삭제 로직 추가
        }
    }
}

function processedLikeAction(el) {
    var table = el.getAttribute('data-table'),
        action = el.getAttribute('data-action'),
        commentNo = el.getAttribute('data-comment');

    var no = articleNo;
    if (table === 'comments') {
        no = commentNo;
    }
    
    if (!table || !action || !no) {
        //console.error('필수 데이터가 누락되었습니다.');
        return false;
    }
    
    var data = {table: table, action: action, no: no};
    var url = '/board/' + boardId + '/like';
    
    sendCustomAjaxRequest('POST', url, data, false)
        .then(response => {
            completedLikeAction(response, el);
        })
        .catch(error => {
            console.error('좋아요 처리 중 오류 발생:', error);
            if (error.response) {
                console.error('에러 응답:', error.response);
            }
            alert('좋아요 처리에 실패했습니다: ' + (error.message || '알 수 없는 오류'));
        });
}

function completedLikeAction(data, el) {
    if (data.result === 'success') {
        updateLikeCount(data, el);  // el을 updateLikeCount에 전달
    } else {
        alert('좋아요 처리에 실패했습니다: ' + (data.message || '알 수 없는 오류'));
    }
}

function updateLikeCount(data, el) {
    // 반응 카운트 요소 찾기
    const reactionCountElement = el.querySelector('.reaction-count');
    
    if (reactionCountElement) {
        // 현재 카운트 가져오기
        let currentCount = parseInt(reactionCountElement.textContent.replace(/[^\d]/g, ''), 10) || 0;
        
        // 모드에 따라 카운트 업데이트
        if (data.data.mode === 'insert') {
            currentCount += 1;
        } else if (data.data.mode === 'delete') {
            currentCount = Math.max(0, currentCount - 1); // 음수 방지
        }
        
        // 업데이트된 카운트를 요소에 적용
        reactionCountElement.textContent = currentCount;
    }
}
</script>
