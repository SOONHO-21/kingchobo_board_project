<?php
include '../inc/common.php';
include '../inc/dbconfig.php';
include '../inc/member.php';
include '../inc/comment.php';   // 댓글 class

if($ses_id == ''){
    $arr = ["result" => "not login"];
    die(json_encode($arr));
}

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';
$idx = (isset($_POST['idx']) && $_POST['idx'] != '') ? $_POST['idx'] : '';
$pidx = (isset($_POST['pidx']) && $_POST['pidx'] != '') ? $_POST['pidx'] : '';
$content = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content'] : '';

if($mode == ''){
    $arr = ["result" => "empty mode"];
    die(json_encode($arr));
}

$comment = new Comment($db);    //댓글(comment) 객체 생성

//댓글 소유권 확인 (인가자만 수정 삭제가 가능하게 처리)
if($mode == 'edit' || $mode == 'delete') {
    if($idx == '') {
        $arr = ["result" => "empty idx"];
        die(json_encode($arr));
    }

    $commentRow = $comment->getinfo(($idx));    //$idx기반 뎃글을 단 정보 가져오기

    if($commentRow['id'] != $ses_id) {
        $arr = ["result" => "access denied"];
        die(json_encode($arr));
    }
}

//댓글 등록
if($mode == 'input') {
    if($pidx == '') {
        $arr = ["result" => "empty pidx"];
        die(json_encode($arr));
    }
    if($content == '') {
        $arr = ["result" => "empty content"];
        die(json_encode($arr));
    }

    $arr = [ "pidx" => $pidx, "content" => $content, "id" => $ses_id ];     //게시글 번호, 댓글 내용, 댓글 작성자 세션 아이디

    $comment->input($arr);      // ../inc/comment.php의 input함수

    $arr = ["result" => "success"];
    die(json_encode($arr));
}
else if($mode == 'edit') {
    if($content == '') {
        $arr = ["result" => "empty content"];
        die(json_encode($arr));
    }

    $arr = [ "idx" => $idx, "content" => $content, "id" => $ses_id ];

    $comment->update($arr);     // ../inc/comment.php의 update함수
    
    $arr = ["result" => "success"];
    die(json_encode($arr));
}
else if($mode == 'delete') {
    if($pidx == '') {
        $arr = ["result" => "empty pidx"];
        die(json_encode($arr));
    }
    
    $comment->delete($pidx, $idx);  // ../inc/comment.php의 update함수

    $arr = ["result" => "success"];
    die(json_encode($arr));
}
?>