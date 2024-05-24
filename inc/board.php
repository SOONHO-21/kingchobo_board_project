<?php
//게시판 관리 클래스

class Board {
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;
    }

    //글목록
    //bcode, id, NAME, SUBJECT, content, hit, create_at
    //now() -> 2023-05-2 11:11:11 현재 연월일시분초
    public function input($arr) {
        $sql = "INSERT INTO board(bcode, id, name, subject, content, ip, create_at) VALUES(
            :bcode, :id, :name, :subject, :content, :ip, :NOW())";
        $stmt =$this->conn->prepare($sql);
        $stmt->bindValue(':bcode', $arr['bcode']);
        $stmt->bindValue(':id', $arr['id']);
        $stmt->bindValue(':name', $arr['name']);
        $stmt->bindValue(':subject', $arr['subject']);
        $stmt->bindValue(':content', $arr['content']);
        $stmt->bindValue(':ip', $arr['ip']);
        $stmt->execute();
    }
}
?>