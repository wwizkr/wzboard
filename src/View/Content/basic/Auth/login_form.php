<div class="member-wrapper">
    <div class="member-wrapper-inner">
    <form name="frm" id="frm" action="/auth/login" method="POST" onsubmit="frm_submit(this);">
    <input type="hidden" name="url" value="<?= $url; ?>">
        <div class="member-login-box">
            <div class="member-login-form">
                <div class="login-form-input">
                    <label for="id">아이디</label>
                    <input type type="text" name="id" id="id" class="input-text input-text-large require" data-type="text" data-message="아이디를 입력해 주세요.">
                </div>
                <div class="login-form-input">
                    <label for="password">비밀번호</label>
                    <input type="password" name="password" id="password" class="input-text input-text-large require" data-type="text" data-message="비밀번호를 입력해 주세요.">
                </div>
                <button type="submit" class="btn btn-fill-accent btn-middle">Login</button>
            </div>
        </div>
    </form>
    <?= $socialProvider; ?>
    </div>
</div>
<script>
function frm_submit(f) {
    if (validateForm(f) === false) {
        return false;
    }

    return true;
}