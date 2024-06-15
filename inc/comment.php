<?php
class Comment {
    private $conn;

    //생성자
    public function __construct($db){
        $this->conn = $db;
    }

    //댓글등록
    public function input($arr) {
        $sql = "INSERT INTO comment (pidx, id, content, create_at, ip) VALUES (
            :pidx, :id, :content, NOW(), :ip)";
        $stmt = $this->conn->prepare($sql);
        $params = [ 
            "pidx" => $arr['pidx'], 
            "id" => $arr['id'], 
            "content" => $arr['content'], 
            "ip" => $_SERVER['REMOTE_ADDR']
        ];

        $stmt->execute($params);
    }
}
?>