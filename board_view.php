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


$boardRow = $board->view($idx);



//$_SERVER["REMOTE_ADDR"] : 지금 접속한 사람의 IP를 담고 있음

if($boardRow['last_reader'] != $_SERVER["REMOTE_ADDR"]) {
    $board->hitInc($idx);
    $board->updateLastReader($idx, $_SERVER["REMOTE_ADDR"]);
}

//다운로드 횟수 -> 저장
$downhit_arr = explode('?', $boardRow['downhit']);


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
            //첨부파일 출력
                if($boardRow['files'] != '') {
                    $filelist = explode('?', $boardRow['files']);

                    //[배열명] = array_fill([시작번호], [채울 항목 수], [값]]);
                    if($boardRow['downhit'] == '') {
                        $downhit_arr = array_fill(0, count($filelist), 0);
                    }

                    $th = 0;
                    foreach($filelist AS $file) {

                        list($file_source, $file_name) = explode('|', $file);

                        echo "<a href=\"./pg/boarddownload.php?idx=$idx&th=$th\">$file_source</a> (down: ".$downhit_arr[$th].")<br>";
                        $th++;
                    }
                }
            ?>
        </div>
        <div class="d-flex gap-2 p-3">
            <button class = "btn btn-secondary" id="btn_list">목록</button>
        <?php if($boardRow['id'] == $ses_id) { ?>
            <button class = "btn btn-primary" id="btn_edit">수정</button>
            <button class = "btn btn-danger" id="btn_delete">삭제</button>
        <?php } ?>
        </div>
    </div>

</main>
<?php
include 'inc_footer.php';
?>