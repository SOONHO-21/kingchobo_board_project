<?php

$g_title = '회원가입을 축하합니다';
$js_array = ['js/member_success.js'];

$menu_code = 'member';

include 'inc_header.php';
?>

<main class="w-75 mx-auto border rounded-5 p-5 d-flex gap-5" style="height: calc(100vh-257px)">
    <img src="images/logo.svg" alt="">
    <div>
        <h1>회원가입을 축하합니다.</h1>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Non consequatur
        iusto ad, ullam debitis sint dolorem dicta velit fugiat asperiores libero
        illo corrupti inventore impedit sed unde est laboriosam nam.</p>
        <button class="btn btn-primary" id="btn_login">로그인 하기</button>
    </div>
</main>

<?php
include 'inc_footer.php';

?>