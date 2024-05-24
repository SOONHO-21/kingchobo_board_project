<?php
$g_title = '순시큐리티';
$js_array = ['js/home.js'];

$menu_code = 'home';

include 'inc/common.php';
include 'inc/dbconfig.php';

//게시판 목록
include 'inc/boardmanage.php';
$boardm = new BoardManage($db);
$boardArr = $boardm->list();

include 'inc_header.php';
?>

<main class="w-75 mx-auto border rounded-5 p-5 d-flex gap-5" style="height: calc(100vh-257px)">
    
    <img src="images/logo.svg" alt="">
    <div>
        <h1>Home 입니다.</h1>
    </div>

</main>

<?php
include 'inc_footer.php';

?>