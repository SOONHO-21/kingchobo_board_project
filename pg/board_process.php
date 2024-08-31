<?php

if(isset($SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > (int) ini_get('post_max_size') * 1024 * 1024) {

    $arr = ['result' => 'post_max_size'];
    die(json_encode($arr));
}

include '../inc/common.php';
include '../inc/dbconfig.php';
include '../inc/board.php';
include '../inc/member.php';

$mode = (isset($_POST['mode']) && $_POST['mode'] != '') ? $_POST['mode'] : '';  //isset: 변수가 설정 되었는지 확인
$bcode = (isset($_POST['bcode']) && $_POST['bcode'] != '') ? $_POST['bcode'] : '';
$subject = (isset($_POST['subject']) && $_POST['subject'] != '') ? $_POST['subject'] : '';
$content = (isset($_POST['content']) && $_POST['content'] != '') ? $_POST['content'] : '';
$idx = (isset($_POST['idx']) && $_POST['idx'] != '' && is_numeric($_POST['idx'])) ? $_POST['idx'] : '';
$th = (isset($_POST['th']) && $_POST['th'] != '' && is_numeric($_POST['th'])) ? $_POST['th'] : '';

if($mode == '') {
    $arr = ["result" => "empty_mode"];
    $json_str = json_encode($arr);  // 배열 => json 문자열
    die($json_str);
}

if($bcode == '') {
    $arr = ["result" => "empty_bcode"];
    die($json_encode($arr));
}

$board = new Board($db);    //Board 객체생성. inc밑에 board.php 
$member = new Member($db);  //Member 객체 생성. inc밑에 member.php. $member 객체 생성

// <p>dddd</p> <img src="djdkeiekdkdkdkd">

if($mode == 'input') {

    //이미지 변환하여 저장하기
    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);
    $img_array = [];
    foreach($matches[1] AS $key => $row){
        if(substr($row, 0, 5) != 'data:') {     //substr( string, start [, length ] ) 문자열, 스타트 인덱스, 5개 문자까지
            continue;
        }

        //data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAI <-이게 $row다
        list($type, $data) = explode(';', $row);    //;기준으로 row 나누기하고 리스트화
        //$type: data:image/png, $data: base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAI
        list(, $data) = explode(',', $data);    //$data를 ','로 다시 나누어 두 번째 부분을 $data에 할당. $data = iVBORw0KGgoAAAANSUhEUgAAAgAAAAI
        $data = base64_decode($data);   //iVBORw0KGgoAAAANSUhEUgAAAgAAAAI 디코딩
        list(, $ext) = explode('/', $type);  //$type(data:image/png)을 /로 나누어 두 번째 부분을 $ext(확장자)에 할당
        $ext = ($ext == 'jpeg') ? 'jpg' : $ext;

        $filename = date('YmdHis').'_'.$key.'.'.$ext;

        file_put_contents(BOARD_DIR."/".$filename, $data);

        $content = str_replace($row, BOARD_WEB_DIR."/".$filename, $content);
        $img_array[] = BOARD_WEB_DIR."/".$filename;
    }


    if($subject == '') {    //제목을 입력 안 했으면
        die(json_encode(["result" => "empty_subject"]));
    }

    if($content == '' || $content == '<p><br></p>') {   //내용을 아무것도 안 썼으면
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
    $file_cnt = 3;
    if(isset($_FILES['files'])) {   //첨부된 파일이 있으면
        $file_list_str = $board->file_attach($_FILES['files'], $file_cnt);  // ../inc/board.php의 file_attach 함수 호출, 파일 목록 문자열에 할당
    }

    $memArr = $member->getInfo($ses_id);    // ../inc/member.php의 file_attach 함수 호출
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

    if($idx == '') {    //파일 인덱스 검증
        $arr = ["result" => "empty_idx"];
        die($json_encode($arr));
    }
    if($th == ''){      //파일 순서 검증
        $arr = ["result" => "empty_th"];
        die($json_encode($arr));
    }

    $file = $board->getAttachFile($idx, $th);   //파일들 정보 가져오기

    $each_files = explode('|', $file);      //파일 배열로 분할

    // BOARD_DIR . '/' . $each_files[0]
    if(file_exists(BOARD_DIR . '/' . $each_files[0])) {
        unlink(BOARD_DIR . '/' . $each_files[0]);
    }

    $row = $board->view($idx);      //$row에 ../inc/board.php의 글 보기 함수에서 반환한 연관 배열 담기(게시글 데이터 가져오기)
    //$row['files']
    $files = explode('?', $row['files']);   //파일 목록을 ? 기준으로 분할하여 배열로 만듦
    $tmp_arr = [];
    foreach($files AS $key => $val) {       //해당 순서($th)의 파일을 제외한 나머지 파일 목록을 재구성

        if($key == $th) {
            continue;
        }

        $tmp_arr[] = $val;
    }

    $files = implode('?', $tmp_arr);    //퍄일리스트 문자열

    //다운로드 수 목록 업데이트
    $tmp_arr = [];
    $downs = explode('?', $row['downhit']);
    foreach($downs AS $key => $val) {

        if($key == $th) {
            continue;
        }

        $tmp_arr[] = $val;
    }

    $downs = implode('?', $tmp_arr);    //다운로드 수 문자열

    $board->updateFileList($idx, $files, $downs);   // ../inc/board.php의 파일 목록 업데이트 함수

    $arr = ["result" => "success"];
    die(json_encode(($arr)));

}
else if($mode == 'file_attach') {
    // 글 수정 -> 개별파일 첨부하기
    
    $file_list_str = '';
    if(isset($_FILES['files'])) {
        $file_cnt = 1;
        $file_list_str = $board->file_attach($_FILES['files'], $file_cnt);  //파일 한 개씩 첨부하는 로직
    } else {
        $arr = [ "result" => "empty_files" ];
        die(json_encode($arr));
    }

    $row = $board->view($idx);  //해당 게시글 가져와서 $row에 담기

    if($row['files'] != '') {
        $files = $row['files'] .'?'. $file_list_str;
    }else {
        $files = $file_list_str;
    }

    if($row['downhit'] != '') {
        $downs = $row['downhit'] .'?0';     //다운로드 수 초기화
    } else {
        $downs = '';
    }

    $board->updateFileList($idx, $files, $downs);

    $arr = [ "result" => "success"];
    die(json_encode($arr));
}
else if($mode == 'edit') {

    $row = $board->view($idx);      //해당 글 정보 $row에 담기
    if($row['id'] != $ses_id) {
        die(json_encode(["result" => "permission_denied"]));
    }
    
    $old_image_arr = $board->extract_image($row['content']);    //기존 이미지 추출

    //이미지 변환하여 저장하기
    preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);
    $current_image_arr = [];
    foreach($matches[1] AS $key => $row){
        if(substr($row, 0, 5) != 'data:') {
            $current_image_arr[] = $row;
            continue;
        }

        // data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAI
        list($type, $data) = explode(';', $row);    //';'를 기준으로 $type에는 data:image/png가, $data에는 base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAI가 저장
        list(,$data) = explode(',', $data);         //$data에는 ','를 기준으로 base64로 코딩된 iVBORw0KGgoAAAANSUhEUgAAAgAAAAI
        $data = base64_decode($data);               //이미지 데이터 base64 디코딩
        list(,$ext) = explode('/', $type);          //$ext에는 /를 기준으로 'png' 저장됨
        $ext = ($ext == 'jpeg') ? 'jpg' : $ext;     //파일 확장자 설정

        $filename = date('YmdHis').'_'.$key.'.'.$ext;

        file_put_contents(BOARD_DIR."/".$filename, $data);  //파일 업로드

        $content = str_replace($row, BOARD_WEB_DIR."/".$filename, $content);    // base64 인코딩된 이미지가 서버 업로드 이름으로 변경
    }

    $diff_img_arr = array_diff($old_image_arr, $current_image_arr);     //배열 비교, 공통된 요소를 제외한 값들을 포함하는 배열을 반환
    foreach($diff_img_arr AS $value) {
        unlink("../".$value);
    }


    if($subject == '') {    //제목이 없으면
        die(json_encode(["result" => "empty_subject"]));
    }

    if($content == '' || $content == '<p><br></p>') {   //내용이 없으면
        die(json_encode(["result" => "empty_content"]));
    }

    $arr = [    //$arr 정의. idx, 제목, 내용
        'idx' => $idx,
        'subject' => $subject,
        'content' => $content
    ];

    $board->edit($arr);     // ../inc/board.php의 edit 함수

    die(json_encode(["result" => "success"]));
}
else if($mode == 'delete') {
    // db 에서 해당 row 삭제
    // 첨부 파일을 삭제
    // 본문에 이미지가 있는 경우 본문 이미지도 삭제를 해야 합니다.

    $row = $board->view($idx);
    //본문이미지 삭제
    $img_arr = $board->extract_image($row['content']);
    foreach($img_arr AS $value) {
        if(file_exists("../".$value)){
            unlink("../".$value);
        }
    }

    //첨부파일 삭제
    if($row['files'] != '') {
        $filelist = explode('?', $row['files']);
        foreach($filelist AS $value) {
            list($file_src, ) = explode('|', $value);   //$value를 '|'로 나눈 부분에서 앞에 부분을 $file_src에 담기
            unlink(BOARD_DIR .'/'. $file_src);
        }
    }

    $board->delete($idx);

    die(json_encode(["result" => "success"]));
}
?>