<?php

if(isset($SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > (int) ini_get('post_max_size') * 1024 * 1024) {

    $arr = ['result' => 'post_max_size'];
    die(json_encode($arr));
}

include '../inc/common.php';
include '../inc/dbconfig.php';
include '../inc/board.php';
include '../inc/member.php';

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';
$bcode = (isset($_POST['bcode']) && $_POST['bcode'] != '') ? $_POST['bcode'] : '';
$subject = (isset($_POST['subject']) && $_POST['subject'] != '') ? $_POST['subject'] : '';
$content = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content'] : '';
$idx = (isset($_POST['idx']) && $_POST['idx'] != '' && is_numeric($_POST['idx'])) ? $_POST['idx'] : '';
$th = (isset($_POST['th']) && $_POST['th'] != '' && is_numeric($_POST['th'])) ? $_POST['th'] : '';

if($mode == '') {
    $arr = ["result" => "empty_mode"];
    $json_str = json_encode($arr);
    die($json_str);
}

if($bcode == '') {
    $arr = ["result" => "empty_bcode"];
    die($json_encode($arr));
}

$board = new Board($db);
$member = new Member($db); // 여기에서 $member 라는 인스턴스가 만들어짐.

//<p>dddd</p> <img src=".....">

if($mode == 'input') {

    //이미지 변환하여 저장하기
    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);
    $img_array = [];
    foreach($matches[1] AS $key => $row){
        if(substr($row, 0, 5) != 'data:') {
            continue;
        }

        //data:image/png;base64,.......
        list($type, $data) = explode(';', $row);
        list(,$data) = explode(';', $data);
        $data = base64_decode($data);
        list(,$ext) = explode('/', $type);
        $ext = ($ext == 'jpeg') ? 'jpg' : $ext;

        $filename = date('YmdHis').'_'.$key.'.'.$ext;

        file_put_contents(BOARD_DIR."/".$filename, $data);

        $content = str_replace($row, BOARD_WEB_DIR."/".$filename, $content);
        $img_array[] = BOARD_WEB_DIR."/".$filename;
    }


    if($subject == '') {
        die(json_encode(["result" => "empty_subject"]));
    }

    if($content == '' || $content == '<p><br></p>') {
        die(json_encode(["result" => "empty_content"]));
    }

    /*
    Array
    (
        [files] => Array
            (
                [name] => Array
                    (
                        [0] => 1.jpg
                        [1] => 2.jpg
                    )
    
                [type] => Array
                    (
                        [0] => image/jpeg
                        [1] => image/jpeg
                    )
                [tmp_name] => Array
                    (
                        [0] => C:\xampp\tmp\php2531.tmp
                        [1] => C:\xampp\tmp\php2532.tmp
                    )
            )
    )
    */

    //파일 첨부
    //$_Files[]
    $file_list_str = '';
    if(isset($_FILES['files'])) {

        $file_list_str = $board->file_attach($_FILES['files'], $file_cnt);
    }

    $memArr = $member->getInfo($ses_id);
    $name = $memArr['name'];

    $arr = [
        'bcode' => $bcode,
        'id' => $ses_id,
        'name' => $name,
        'subject' => $subject,
        'content' => $content,
        'files' => $file_list_str,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    $board->input($arr);

    die(json_encode(["result" => "success"]));
}
else if($mode == 'each_file_del') {

    if($idx == '') {
        $arr = ["result" => "empty_idx"];
        die($json_encode($arr));
    }
    if($th == ''){
        $arr = ["result" => "empty_th"];
        die($json_encode($arr));
    }

    $file = $board->getAttachFile($idx, $th);

    $each_files = explode('|', $file);

    // BOARD_DIR . '/' . $each_files[0]
    if(file_exists(BOARD_DIR . '/' . $each_files[0])) {
        unlink(BOARD_DIR . '/' . $each_files[0]);
    }

    $row = $board->view($idx);
    //$row['files']
    $files = explode('?', $row['files']);
    $tmp_arr = [];
    foreach($files AS $key => $val) {

        if($key == $th) {
            continue;
        }

        $tmp_arr[] = $val;
    }

    $files = implode('?', $tmp_arr);    //퍄일리스트 문자열

    $tmp_arr = [];
    $downs = explode('?', $row['downhit']);
    foreach($downs AS $key => $val) {

        if($key == $th) {
            continue;
        }

        $tmp_arr[] = $val;
    }

    $downs = implode('?', $tmp_arr);    //다운로드 수 문자열

    $board->updateFileList($idx, $files, $downs);

    $arr = ["result" => "success"];
    die(json_encode(($arr)));

}
else if($mode == 'file_attach') {
    // 수정에서 개별파일 첨부하기
    
    $file_list_str = '';
    if(isset($_FILES['files'])) {
        $file_cnt = 1;
        $file_list_str = $board->file_attach($_FILES['files'], $file_cnt);
    } else {
        $arr = [ "result" => "empty_files"];
        die(json_encode($arr));
    }

    $row = $board->view($idx);

    if($row['files'] != '') {
        $files = $row['files'] .'?'. $file_list_str;
    }else {
        $files = $file_list_str;
    }

    if($row['downhit'] != '') {
        $downs = $row['downhit'] .'?0';
    } else {
        $downs = '';
    }

    $board->updateFileList($idx, $files, $downs);

    $arr = [ "result" => "success"];
    die(json_encode($arr));
}
else if($mode == 'edit') {

    $row = $board->view($idx);
    if($row['id'] != $ses_id) {
        die(json_encode(["result" => "permission_denied"]));
    }

    //이미지 변환하여 저장하기
    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);
    $current_image_arr = [];
    $img_array = [];
    foreach($matches[1] AS $key => $row){
        if(substr($row, 0, 5) != 'data:') {
            $current_image_arr[] = $row;
            continue;
        }

        //data:image/png;base64,.......
        list($type, $data) = explode(';', $row);
        list(,$data) = explode(',', $data);
        $data = base64_decode($data);
        list(,$ext) = explode('/', $type);
        $ext = ($ext == 'jpeg') ? 'jpg' : $ext;

        $filename = date('YmdHis').'_'.$key.'.'.$ext;

        file_put_contents(BOARD_DIR."/".$filename, $data);  //파일업로드

        $content = str_replace($row, BOARD_WEB_DIR."/".$filename, $content);    //base64 인코딩된 이미지가 서버 업로드 이미지로 변경
        $img_array[] = BOARD_WEB_DIR."/".$filename;
    }


    if($subject == '') {
        die(json_encode(["result" => "empty_subject"]));
    }

    if($content == '' || $content == '<p><br></p>') {
        die(json_encode(["result" => "empty_content"]));
    }

    $arr = [
        'idx' => $idx,
        'subject' => $subject,
        'content' => $content
    ];

    $board->edit($arr);

    die(json_encode(["result" => "success"]));
}
?>