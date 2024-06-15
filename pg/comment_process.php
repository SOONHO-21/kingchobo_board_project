<?php
include '../inc/common.php';
include '../inc/dbconfig.php';
include '../inc/member.php';
include '../inc/comment.php'; // 댓글 class

if($ses_id == ''){
    $arr = ["result" => "not login"];
    die(json_encode($arr));
}

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';
$idx = (isset($_POST['idx']) && $_POST['idx'] != '') ? $_POST['idx'] : '';
$pidx = (isset($_POST['pidx']) && $_POST['pidx'] != '') ? $_POST['pidx'] : '';
$content = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content'] : '';

//댓글 등록
if($mode == 'input') {
    if($idx == '') {
        $arr = ["result" => "empty idx"];
        die(json_encode($arr));
    }
    if($content == '') {
        $arr = ["result" => "empty content"];
        die(json_encode($arr));
    }

    $arr = ["pidx" => $pidx, "content" => $content, "id" => $ses_id];

    $comment = new Comment($db);
    $comment->input($arr);
}
?>