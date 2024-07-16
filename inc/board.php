<?php
//게시판 관리 클래스

class Board {
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;      //생성자에 전달된 $db 값을 현재 객체의 conn 속성에 할당
    }

    //글 등록
    //bcode, id, NAME, SUBJECT, content, hit, create_at
    //now() -> 2023-05-2 11:11:11 현재 연월일시분초
    public function input($arr) {   // 매개변수는 arr 배열
        $sql = "INSERT INTO board( bcode, id, name, subject, content, files, ip, create_at) VALUES(
            :bcode, :id, :name, :subject, :content, :files, :ip, NOW())";
        $stmt =$this->conn->prepare($sql);
        $stmt->bindValue(':bcode', $arr['bcode']);  // arr 배열 요소들에 SQL 쿼리의 VALUE 할당
        $stmt->bindValue(':id', $arr['id']);
        $stmt->bindValue(':name', $arr['name']);
        $stmt->bindValue(':subject', $arr['subject']);
        $stmt->bindValue(':content', $arr['content']);
        $stmt->bindValue(':files', $arr['files']);
        $stmt->bindValue(':ip', $arr['ip']);
        $stmt->execute();       //execute 메서드는 준비된 SQL 문을 실행
    }

    //글 수정
    public function edit($arr) {
        $sql = "UPDATE board SET subject=:subject, content=:content WHERE idx=:idx";    //sql 쿼리 정의
        //prepare() : PDO(PHP Data Objects)에서 제공하는 메소드. SQL 문을 준비
        $stmt = $this->conn->prepare($sql);
        // execute 메서드에 전달할 파라미터 배열을 정의
        $params = [':subject' => $arr['subject'], ':content' => $arr['content'], ':idx' => $arr['idx']];
        $stmt->execute($params);
    }

    //글 목록
    public function list($bcode, $page, $limit, $paramArr) {    //$paramArr에는 sn, sf 값이 필요
        $start = ($page - 1) * $limit;

        $where = "WHERE bcode=:bcode ";
        $params = [':bcode' => $bcode];

        //이 if문에서는 정의할 sql문인 $where, execute에 전달할 파라미터 배열 $params을 정의
        if(isset($paramArr['sn']) && $paramArr['sn'] != '' && isset($paramArr['sf']) && $paramArr['sf'] != '') {
            switch($paramArr['sn']) {
                case 1 :
                    $where .= "AND (subject LIKE CONCAT('%', :sf, '%') OR (content LIKE CONCAT('%', :sf2, '%'))) ";
                    $params = [':bcode' => $bcode ,':sf' => $paramArr['sf'], ':sf2' => $paramArr['sf']];
                break;

                case 2 :
                    $where .= "AND (subject LIKE CONCAT('%', :sf, '%')) "; 
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;
                
                case 3 :
                    $where .= "AND (content LIKE CONCAT('%', :sf, '%')) "; 
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;

                case 4 :
                    $where .= "AND (name=:sf)";
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;
            }

        }

        $sql = "SELECT idx, id, subject, name, hit, comment_cnt, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') AS create_at
        FROM board ". $where ." ORDER BY idx DESC LIMIT " . $start . "," . $limit;

        //$stmt는 PDOStatement 객체. prepare 메서드는 PDOStatement 객체를 반환
        //PDOStatement 객체 : execute, fetch, fetchAll 등 여러 메서드를 제공, SQL 문을 실행 및 결과를 처리할 수 있게 함
        $stmt = $this->conn->prepare($sql);

        //setFetchMode : 연관 배열로 결과를 가져오도록 지시. 즉, 결과 셋의 각 행이 컬럼 이름을 키로 하는 배열로 반환
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute($params);
        return $stmt->fetchAll();   //모든 결과 행을 가져와 연관 배열의 배열 형태로 반환
    }

    //전체글 수 구하기
    public function total($bcode, $paramArr) {

        $where = "WHERE bcode=:bcode ";

        $params = [':bcode' => $bcode];
        if(isset($paramArr['sn']) && $paramArr['sn'] != '' && isset($paramArr['sf']) && $paramArr['sf'] != '') {
            switch($paramArr['sn']) {
                case 1 :
                    $where .= "AND (subject LIKE CONCAT('%', :sf, '%') OR (content LIKE CONCAT('%', :sf2, '%'))) ";
                    $params = [':bcode' => $bcode ,':sf' => $paramArr['sf'], ':sf2' => $paramArr['sf']];
                break;

                case 2 :
                    $where .= "AND (subject LIKE CONCAT('%', :sf, '%')) ";
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;
                
                case 3 :
                    $where .= "AND (content LIKE CONCAT('%', :sf, '%')) "; 
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;

                case 4 :
                    $where .= "AND (name=:sf)";
                    $params = [':bcode' => $bcode , ':sf' => $paramArr['sf']];
                break;
            }

        }

        $sql = "SELECT COUNT(*) AS cnt FROM board ". $where;

        $stmt = $this->conn->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['cnt'];
    }

    // 글보기
    public function view($idx) {
        $sql = "SELECT * FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute($params);
        return $stmt->fetch();    
    }

    //글 조회수 +1
    public function hitInc($idx) {
        $sql = "UPDATE board SET hit=hit +1 WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
    }

    //파일 목록 업데이트
    public function updateFileList($idx, $files, $downs) {
        $sql = "UPDATE board SET files=:files, downhit=:downs WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx, ":files" => $files, ":downs" => $downs];
        $stmt->execute($params);
    }

    //첨부파일들 정보 구하기
    public function getAttachFile($idx, $th) {
        $sql = "SELECT files FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();

        $filelist = explode('?', $row['files']);    //explode : 문자열 나누기. aaa.jpg|이름?.. | ..

        return $filelist[$th] . '|' . count($filelist);
    }

    //게시글에 첨부된 파일들의 다운로드 회수 구하기
    public function getDownhit($idx){
        $sql = "SELECT downhit FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();

        return $row['downhit'];
    }

    //다운로드 횟수 증가시키기(DB에 반영하기)
    public function increaseDownhit($idx, $downhit) {
        $sql = "UPDATE board SET downhit=:downhit WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":downhit" => $downhit, ":idx" => $idx];
        $stmt->execute($params);
    }

    //last reader 값 변경
    public function updateLastReader($idx, $str) {
        $sql = "UPDATE board SET last_reader=:last_reader WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":last_reader" => $str, ":idx" => $idx];
        $stmt->execute($params);
    }

    //파일 첨부
    public function file_attach($files, $file_cnt) {

        if(sizeof($files['name']) > $file_cnt) {
            //연관 배열을 생성하여 $arr 변수에 할당. result라는 키와 file_upload_count_exeed라는 값을 가진다
            $arr = ["result" => "file_upload_count_exeed"];
            die(json_encode(($arr)));   //에러 메시지 출력
        }

        //파일 이름 및 확장자 처리하는 부분
        $tmp_arr = [];
        foreach($files['name'] AS $key => $val){
            $full_str = '';

            //explode함수는 문자열을 구분자(.)를 기준으로 나누어 배열로 반환
            //$files 배열 : 업로드된 파일 정보를 포함
            //name : 파일의 원래 이름을 나타내며, $key는 현재 파일의 인덱스를 가리킴
            //$tmparr이 파일명을 '.'을 기준으로 나눠진 요소들을 담는 배열이다.
            $tmparr = explode('.', $files['name'][$key]);   //$files['name'][$key]. PHP에서 파일 업로드를 처리할 때 사용하는 $_FILES 배열은 다차원 배열
            //배열의 마지막 요소 반환
            $ext = end($tmparr);

            //허용되지 않는 확장자 설정
            $not_allowed_file_ext = ['txt', 'exe', 'xls'];

            if(in_array($ext, $not_allowed_file_ext)) {
                $arr = ['result' => 'not_allowed_file'];    //허용되지 않는 확장자가 들어오면
                die(json_encode($arr));     //에러 메시지 출력
            }

            $flag = rand(1000, 9999);   //플래그값을 1000~9999값 사이로 출력
            $filename = 'a'. date('YmdHis') . $flag . '.' . $ext;   //파일명 정의
            $file_ori = $files['name'][$key];   //원본 파일
            // a12804128138.jpg|새파일.jpg

            // copy() move_uploaded_file() 파일을 BOARD_DIR 디렉토리로 복사한다.
            copy($files['tmp_name'][$key], BOARD_DIR . '/' . $filename);
            
            $full_str = $filename . '|' . $file_ori;
            $tmp_arr[] = $full_str;
        }

        return implode('?', $tmp_arr);
    }

    public function extract_image($content) {       //글 내용에서 이미지 추출
        preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $content, $matches);
        $img_array = [];
        foreach($matches[1] AS $key => $row){
            $img_array[] = $row;
        }
        return $img_array;
    }

    public function delete($idx) {
        $sql = "DELETE FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [':idx' => $idx];
        $stmt->execute($params);
    }
}
?>