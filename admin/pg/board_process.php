<?php
include '../inc_common.php';
include '../../inc/dbconfig.php';
include '../../inc/board.php';

$board_title = (isset($_POST['board_title']) && $_POST['board_title'] != '') ? $_POST['board_title'] : '';
$board_type = (isset($_POST['board_type']) && $_POST['board_type'] != '') ? $_POST['board_type'] : '';
$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';

if($mode == '') {
    $arr = ["result" => "mode_empty"];
    die(json_encode($arr));
}

$board = new Board($db);

//게시판 생성
if($mode == 'input') {
    if($board_title == '') {
        $arr = ["result" => "title_empty"];
        die(json_encode($arr));
    }

    if($board_type == '') {
        $arr = ["result" => "btype_empty"];
        die(json_encode($arr));
    }

    //게시판 코드 생성
    $bcode = $board->bcode_create();

    //게시판 생성
    $arr = [
        "name" => $board_title,
        "btype" => $board_type,
        "bcode" => $bcode
    ];

    $board->create($arr);

    $arr = ["result" => "success"];
    die(json_encode($arr));
}
?>