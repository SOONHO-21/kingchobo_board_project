<?php
//회원목록
class BoardManage {
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;
    }

    //게시판 목록
    public function list() {
        $sql = "SELECT idx, name, bcode, btype, cnt, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') AS create_at
        FROM board_manage
        ORDER BY idx ASC";

        $stmt = $this->conn->prepare($sql);     //$stmt는 PDOStatement 객체
        $stmt->setFetchMode(PDO::FETCH_ASSOC);  //SQL문을 연관 배열로 반환
        $stmt->execute();
        return $stmt->fetchAll();
    }

    //게시판 생성
    public function create($arr) {
        $sql = "INSERT INTO  board_manage(name, bcode, btype, create_at) values
            (:name, :bcode, :btype, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $arr['name']);    //bindParam : SQL 문에 있는 위치 홀더(:name, :bcode, :btype)에 배열 $arr의 값들을 바인딩
        $stmt->bindParam(':bcode', $arr['bcode']);
        $stmt->bindParam(':btype', $arr['btype']);
        $stmt->execute();
    }

    //게시판 정보 수정
    public function update($arr){
        $sql = "UPDATE board_manage SET name=:name, btype=:btype WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        //$arr의 값들을 바인딩
        $stmt->bindValue(':name', $arr['name']);
        $stmt->bindValue(':btype', $arr['btype']);
        $stmt->bindValue(':idx', $arr['idx']);
        $stmt->execute();
    }

    //게시판 idx로 게시판 정보 가져오기
    public function getBcode($idx) {
        $sql = "SELECT bcode FROM board_manage WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        //bindValue와 bindParam의 차이점: bindValue는 즉시 값을 바인딩하고, bindParam은 참조로 바인딩
        $stmt->bindParam(':idx', $idx);
        //페치 모드를 설정하고, 첫 번째 열(인덱스 0)만 가져옴
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);  //컬럼을 가져온다, 0번 째 열을 가져온다
        $stmt->execute();
        return $stmt->fetch();
    }

    //게시판 삭제
    public function delete($idx) {
        //bcode
        $bcode = $this->getBcode($idx);
        
        $sql = "DELETE FROM board_manage where idx=:idx";   //게시판 관리 DB에서 삭제
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idx', $idx);
        $stmt->execute();

        $sql = "DELETE FROM board where bcode=:bcode";      //게시판 DB에서 삭제
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':bcode', $bcode);
        $stmt->execute();
    }

    //게시판 코드 생성
    public function bcode_create() {
        $letter = range('a', 'z');  //알파벳 소문자 'a'부터 'z'까지의 문자 배열을 생성
        $bcode = '';
        for($i = 0; $i < 6; $i++) { //6자리 코드 설정
            $r = rand(0, 25);       //0~25 랜덤한 수 배열 설정
            $bcode .= $letter[$r];  //$letter[$r] : 무작위로 선택된 문자를 가져옴
        }
        return $bcode;
    }

    //게시판 정보 불러오기
    public function getInfo($idx){
        $sql = "SELECT * FROM board_manage WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idx', $idx);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    //게시판 코드로 게시판 명 가져오기
    public function getBoardName($bcode) {
        $sql = "SELECT name FROM board_manage WHERE bcode=:bcode";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':bcode', $bcode);
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>