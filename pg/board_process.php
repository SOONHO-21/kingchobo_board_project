<?php
include '../inc/common.php';
include '../inc/dbconfig.php';
include '../inc/board.php';
include '../inc/member.php';

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';
$bcode = (isset($_POST['bcode']) && $_POST['bcode'] != '') ? $_POST['bcode'] : '';
$subject = (isset($_POST['subject']) && $_POST['subject'] != '') ? $_POST['subject'] : '';
$content = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content'] : '';

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

    //파일 첨부
    //$_Files[]
    if(isset($_FILES['files']) && $_FILES['files']['name'] != '') {
        $tmparr = explode('.', $_FILES['files']['name']);
        $ext = end($tmparr);
        $flag = rand(1000, 9999);
        $filename = 'a'.date('YmdHis').$flag.'.'. $ext;
        $file_ori = $_FILES['files']['name'];
        // a12804128138.jpg|새파일.jpg

        copy($_FILES['files']['tmp_name'], BOARD_DIR . "/" . $filename); 
        
        $full_str = $filename . '|' . $file_ori;
    }

    $memArr = $member->getInfo($ses_id);
    $name = $memArr['name'];

    $arr = [
        'bcode' => $bcode,
        'id' => $ses_id,
        'name' => $name,
        'subject' => $subject,
        'content' => $content,
        'files' => $full_str,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    $board->input($arr);

    die(json_encode(["result" => "success"]));
}
?>