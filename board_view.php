<?php

include 'inc/common.php';
include 'inc/dbconfig.php';
include 'inc/board.php';
include 'inc/lib.php'; // 페이지네이션

$bcode = (isset($_GET['bcode']) && $_GET['bcode'] != '') ? $_GET['bcode'] : '';
$idx = (isset($_GET['idx']) && $_GET['idx'] != '' && is_numeric($_GET['idx'])) ? $_GET['idx'] : '';

if($bcode == '') {
    die('<script>alert("게시판 코드가 빠졌습니다");history.go(-1)</script>');
}

if($idx == '') {
    die('<script>alert("게시물 번호가 빠졌습니다");history.go(-1)</script>');
}

//게시판 목록
include 'inc/boardmanage.php';
$boardm = new BoardManage($db);
$boardArr = $boardm->list();
$board_name = $boardm->getBoardName($bcode);

$board = new Board($db);
$menu_code = 'board';
$js_array = ['js/board_view.js'];
$g_title = $board_name;

$boardRow = $board->hitInc($idx);
$boardRow = $board->view($idx);

include_once 'inc_header.php';

?>
<main class="w-50 mx-auto border rounded-5 p-5">
    <h1 class="text-center"><?= $board_name ?></h1>

    <div class="vstack w-75 mx-auto">
        <div class="p-3">
            <span class="h3 fw-bolder"><?= $boardRow['name']; ?></span>
        </div>
        <div class="d-flex border border-top-0 border-start-0 border-end-0 border-bottom-1">
            <span><?= $boardRow['name']; ?></span>
            <span class="ms-5 me-auto"><?= $boardRow['hit']; ?>회</span>
            <span><?= $boardRow['create_at']; ?></span>
        </div>
        <div class="p-3">
            <?= $boardRow['content']; ?>

            <?php
                if($boardRow['files'] != '') {
                    $filelist = explode('?', $boardRow['files']);

                    $th = 0;
                    foreach($filelist AS $file) {
                        list($file_source, $file_name) = explode('|', $file);

                        echo "<a href=\"./pg/boarddownload.php?idx=$idx&th=$th\">$file_source</a><br>";
                        $th++;
                    }
                }
            ?>
        </div>
        <div class="d-flex gap-2 p-3">
            <button class = "btn btn-secondary">목록</button>
            <button class = "btn btn-primary">수정</button>
            <button class = "btn btn-danger">삭제</button>
        </div>
    </div>

</main>
<?php
include 'inc_footer.php';
?>