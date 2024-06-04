<?php
//게시판 관리 클래스

class Board {
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;
    }

    //글 등록
    //bcode, id, NAME, SUBJECT, content, hit, create_at
    //now() -> 2023-05-2 11:11:11 현재 연월일시분초
    public function input($arr) {
        $sql = "INSERT INTO board( bcode, id, name, subject, content, files, ip, create_at) VALUES(
            :bcode, :id, :name, :subject, :content, :files, :ip, NOW())";
        $stmt =$this->conn->prepare($sql);
        $stmt->bindValue(':bcode', $arr['bcode']);
        $stmt->bindValue(':id', $arr['id']);
        $stmt->bindValue(':name', $arr['name']);
        $stmt->bindValue(':subject', $arr['subject']);
        $stmt->bindValue(':content', $arr['content']);
        $stmt->bindValue(':files', $arr['files']);
        $stmt->bindValue(':ip', $arr['ip']);
        $stmt->execute();
    }

    //글 목록
    public function list($bcode, $page, $limit, $paramArr) {
        $start = ($page - 1) * $limit;

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

        $sql = "SELECT idx, id, subject, name, hit, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') AS create_at
        FROM board ". $where ."
        ORDER BY idx DESC LIMIT " . $start . "," . $limit;

        $stmt = $this->conn->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute($params);
        return $stmt->fetchAll();
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

        $sql = "SELECT COUNT(*) AS cnt
        FROM board ". $where;

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
        $sql = "UPDATE board SET hit=hit+1 WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
    }

    //첨부파일 구하기
    public function getAttachFile($idx, $th) {
        $sql = "SELECT * FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();

        $filelist = explode('?', $row['files']);    //aaa.jpg|이름?.. | ..

        return $filelist[$th] . '|' . count($filelist);
    }

    //다운로드 회수 구하기
    public function getDownhit($idx){
        $sql = "SELECT downhit FROM board WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":idx" => $idx];
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();

        return $row['downhit'];
    }

    //다운로드 횟수 증가시키기
    public function increaseDownhit($idx, $downhit) {
        $sql = "UPDATE board SET downhit=:downhit WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":downhit" => $downhit, ":idx" => $idx];
        $stmt->execute($params);
    }
    public function updateLastReader($idx, $str) {
        $sql = "UPDATE board SET last_reader=:last_reader WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $params = [":last_reader" => $str, ":idx" => $idx];
        $stmt->execute($params);
    }
    
}
?>