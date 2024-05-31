<?php
$idx = (isset($_GET['idx']) && $_GET['idx'] != '' && is_numeric($_GET['idx'])) ? $_GET['idx'] : '';
$th = (isset($_GET['th']) && $_GET['th'] != '' && is_numeric($_GET['th'])) ? $_GET['th'] : '';

if($idx == '') {
    die('<script>alert("게시물 번호가 빠졌습니다.")</script>');
}

if($idx == '') {
    die('<script>alert("몇 번째 파일인지 알 수가 없습니다.")</script>');
}

include '../inc/dbconfig.php';
include '../inc/board.php';

$board = new Board($db);

$fileinfo = $board->getAttachFile($idx, $th);
list($file_source, $file_name) = explode('|', $fileinfo);

if($file_source == '' || $file_name == '') {
    die('<script>alert("정보를 제대로 가져오지 못했습니다.")</script>');
}

$down = BOARD_DIR . '/' . $file_source;

if(!file_exists($down)) {
    die('<script>alert("존재하지 않는 파일입니다.")</script>');
}

$filesize = filesize($down);

header("Content-Type:application/octet-stream");
header("Content-Disposition:attachment;filename=$file_name");
header("Content-Transfer-Encoding:binary");
header("Content-Length:$filesize");
header("Cache-Control:cache,must-revalidate");
header("Pragma:no-cache");

$fp = fopen($down, "r");
while(!feof($fp)) {
    $buf = fread($fp, 8096);
    print($buf);
    flush();
}

fclose($fp)
?>