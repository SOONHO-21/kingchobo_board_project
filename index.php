<?php
session_start();

$ses_id = (isset($_SESSION['ses_id']) && $_SESSION['ses_id'] != '') ? $_SESSION['ses_id'] : '';
$ses_level = (isset($_SESSION['ses_level']) && $_SESSION['ses_level'] != '') ? $_SESSION['ses_level'] : '';

$g_title = '순시큐리티';
$js_array = ['js/home.js'];

$menu_code = 'home';

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