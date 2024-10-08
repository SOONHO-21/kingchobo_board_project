<?php
include 'inc/common.php';
include 'inc/dbconfig.php';
include 'inc/board.php';
include 'inc/comment.php';
include 'inc/lib.php'; // 페이지네이션


$bcode = (isset($_GET['bcode']) && $_GET['bcode'] != '') ? $_GET['bcode'] : '';         //게시물 코드
$idx = (isset($_GET['idx']) && $_GET['idx'] != '' && is_numeric($_GET['idx'])) ? $_GET['idx'] : ''; //게시물 번호

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

if($boardRow == null) {
    die('<script>alert("존재하지 않는 게시물입니다.");history.go(-1);</script>');
}

//댓글 목록
$comment = new Comment($db);

$commentRs = $comment->list($idx);


//$_SERVER["REMOTE_ADDR"] : 지금 접속한 사람의 IP를 담고 있음

if($boardRow['last_reader'] != $_SERVER["REMOTE_ADDR"]) {
    $board->hitInc($idx);
    $board->updateLastReader($idx, $_SERVER["REMOTE_ADDR"]);
}

//다운로드 횟수 저장 배열
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
                if($boardRow['files'] != '') {      //첨부파일이 있으면
                    $filelist = explode('?', $boardRow['files']);   //파일 목록 정보 가져오기

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

        <div class="d-flex gap-2 mt-3">
            <textarea name="" rows="3" class="form-control" id="comment_content"></textarea>
            <button class="btn btn-secondary" id="btn_comment" data-comment-idx="0">등록</button>
        </div>

        <!--댓글 등록 및 수정, 삭제 부분-->
        <div class="mt-3">

        <table class="table">
            <colgroup>
            <col width="50%" />
            <col width="10%" />
            <col width="10%" />
            </colgroup>
            <?php
                foreach($commentRs AS $comRow) {    //$commentRs : 댓글 목록 배열 $comRow : 각 댓글
            ?>
            <tr>
                <td>
                <span><?php echo nl2br($comRow['content']); ?></span>   <!--댓글 내용의치환된 문자열 반환 \n 처리-->

                <?php
                    if($comRow['id'] == $ses_id) {
                        echo '
                        <button class="btn btn-info p-1 btn-sm btn_comment_edit" data-comment-idx="'.$comRow['idx'].'">수정</button>
                        <button class="btn btn-danger p-1 btn-sm ms-2 btn_comment_delete" data-comment-idx="'.$comRow['idx'].'">삭제</button>';
                    }
                ?>
                </td>
                <td><?php echo $comRow['id']; ?></td>
                <td><?php echo $comRow['create_at']; ?></td>
            </tr>
            <?php } ?>
        </table>

        </div>

    </div>

</main>
<?php
include 'inc_footer.php';
?>