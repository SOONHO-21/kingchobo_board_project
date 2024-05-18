<?php
//회원목록
class Board {
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;
    }
    public function list() {
        $sql = "SELECT idx, name, bcode, btype, cnt, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') AS create_at
        FROM board_manage
        ORDER BY idx ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    //게시판 생성
    public function create($arr) {
        $sql = "INSERT INTO  board_manage(name, bcode, btype, create_at) values
            (:name, :bcode, :btype, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $arr['name']);
        $stmt->bindParam(':bcode', $arr['bcode']);
        $stmt->bindParam(':btype', $arr['btype']);
        $stmt->execute();
    }

    //게시판 코드 생성
    public function bcode_create() {
        $letter = range('a', 'z');
        $bcode = '';
        for($i = 0; $i < 6; $i++) {
            $r = rand(0, 25);
            $bcode .= $letter[$r];
        }
        return $bcode;
    }
}
?>