<?php

include 'inc/common.php';
include 'inc/dbconfig.php';
include 'inc/board.php';

$bcode = (isset($_GET['bcode']) && $_GET['bcode'] != '') ? $_GET['bcode'] : '';
if($bcode == '') {
    die('<script>alert("게시판 코드가 빠짐");history.go(-1)</script>');
}

$idx = (isset($_GET['idx']) && $_GET['idx'] != '') ? $_GET['idx'] : '';
if($idx == '') {
    die('<script>alert("게시판 번호가 빠짐");history.go(-1)</script>');
}

//게시판 목록
include 'inc/boardmanage.php';
$boardm = new BoardManage($db);     //BoardManage 객체 생성
$boardArr = $boardm->list();
$board_name = $boardm->getBoardName($bcode);    //게시판 코드 기반 게시판 이름 얻기


//게시판
$board = new Board($db);
$boardRow = $board->view($idx);     //게시판 글 보기

if($boardRow['id'] != $ses_id) {
?>
    <script>
        alert("본인의 게시물이 아닙니다. 수정하실 수 없습니다.")
        self.location.href='./board.php?bcode=<?= $bcode; ?>'
    </script>
<?php
    exit;
}

$boardRow['content'] = str_replace('`', '\`', $boardRow['content']);    //글 내용에서 \(역슬래시), '`'문자 처리


$js_array = ['js/board_edit.js'];   //글 수정 js코드 본 PHP 코드에 포함시키기

$g_title = '게시판';

include_once 'inc_header.php';

?>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<main class="w-75 mx-auto border rounded-5 p-5">
    <h1 class="text-center">게시판 글수정</h1>
    
    <div class="mb-3">  <!--제목 입력-->
        <input type="text" name="subject" id="id_subject" value="<?= $boardRow['subject']; ?>" class="form-control" placeholder="제목을 입력하세요" autocomplete="off">
    </div>
    <div id="summernote"></div>

    <div class="mt-3">
    <?php
    //첨부파일 출력
        $th = 0;    //첨부파일 번호
        if($boardRow['files'] != '') {
            $filelist = explode('?', $boardRow['files']);   //파일 명 목록

            //[배열명] = array_fill([시작번호], [채울 항목 수], [값]]);
            if($boardRow['downhit'] == '') {    //파일 다운로드 수가 없으면
                $downhit_arr = array_fill(0, count($filelist), 0);  //파일들의 다운로드 수 0으로 초기화
            }

            foreach($filelist AS $file) {

                list($file_source, $file_name) = explode('|', $file);   //파일 경로, 이름 분리해서 각각에 담기

                echo "<a href=\"./pg/boarddownload.php?idx=$idx&th=$th\">$file_name</a>     //파일 다운로드 링크
                <button class='btn btn-sm btn-danger mb-2 btn_file_del py-0' data-th='".$th."'>삭제</button><br>";  //파일 삭제 버튼
                $th++;
            }
        }
    ?>

    </div>
    <?php if($th < 3) { ?>  <!--파일은 최대 3개 까지-->
        <div class="mt-3">
            <input type="file" name="attach" id="id_attach" class="form-control">
        </div>
    <?php }?>
    <div class="mt-3 d-flex gap-2 justify-content-end">
        <button class="btn btn-primary" id="btn_edit_submit">확인</button>
        <button class="btn btn-secondary" id="btn_board_list">목록</button>
    </div>

</main>

    <script>
        $('#summernote').summernote({
            placeholder: '내용을 입력해 주세요',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
      
        var markupStr = `<?= $boardRow['content']; ?>`;
        $('#summernote').summernote('code', markupStr);

    </script>
<?php
include 'inc_footer.php';
?>