<?php
//Member class file

class Member {
    //멤버 변수, 프로피터
    private $conn;

    //생성자
    public function __construct($db) {
        $this->conn = $db;
    }

    //아이디 중복체크용 멤버 함수, 메소드
    public function id_exists($id){
        $sql = "SELECT * FROM member where id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;    //rowCount: 행의 수. 조회되는 행이 하나라도 있으면 즉, ID가 하나라도 있으면 false를 반환
    }

    //이메일 형식 체크
    public function email_format_check($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);   //php 내장 유효성 검사 함수. filter_var('검사할 값', FILTER_VALIDATE_EMAIL);
    }

    public function email_exists($email){
        $sql = "SELECT * FROM member where email=:email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    //회원 정보 입력
    public function input($marr) {

        //단방향 암호화
        $new_hash_password = password_hash($marr['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO member(id, name, password, email, zipcode, addr1, addr2, photo, create_at, ip) values
                (:id, :name, :password, :email, :zipcode, :addr1, :addr2, :photo, now(), :ip)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $marr['email']);
        $stmt->bindParam(':id', $marr['id']);
        $stmt->bindParam(':name', $marr['name']);
        $stmt->bindParam(':password', $new_hash_password);
        $stmt->bindParam(':email', $marr['email']);
        $stmt->bindParam(':zipcode', $marr['zipcode']);
        $stmt->bindParam(':addr1', $marr['addr1']);
        $stmt->bindParam(':addr2', $marr['addr2']);
        $stmt->bindParam(':photo', $marr['photo']);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
    }

    //로그인 함수
    public function login($id, $pw){
        $sql = "SELECT * FROM member where id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        
        if($stmt->rowCount()){
            $row = $stmt->fetch();

            //password_verify($password, $new_password)
            if(password_verify($pw, $row['password'])) {    //패스워드가 일치하면
                $sql = "UPDATE member SET login_dt=NOW() WHERE id=:id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                return true;
            } else {        //패스워드가 틀리면 false 반환
                return false;
            }
        } else {
            return false;
        }
    }

    public function logout(){
        session_start();    //세션 실행
        session_destroy();  //세션에 등록된 데이터 파기

        die('<script>self.location.href="../index.php";</script>');
    }

    public function getInfoFromIdx($idx) {      //idx기반 회원정보 조회
        $sql = "SELECT * FROM member WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":idx", $idx);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getInfo($id) {
        $sql = "SELECT * FROM member WHERE id=:id";     //id기반 회원정보 조회
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function edit($marr) {
        $sql = "UPDATE member SET name=:name, email=:email, zipcode=:zipcode, addr1=:addr1, addr2=:addr2, photo=:photo";
        $params = [
            ':name' => $marr['name'],
            ':email' => $marr['email'],
            ':zipcode' => $marr['zipcode'],
            ':addr1' => $marr['addr1'],
            ':addr2' => $marr['addr2'],
            ':photo' => $marr['photo']
        ];

        if($marr['password'] != ''){
            //단방향 암호화
            $new_hash_password = password_hash($marr['password'], PASSWORD_DEFAULT);    //PASSWORD_DEFAULT : bcrypt 알고리즘을 사용
            $params[':password'] = $new_hash_password;

            $sql .= ", password=:password";
        }

        if($_SESSION['ses_level'] == 10 && isset($marr['idx']) && $marr['idx'] != '') {     //ses_level이 10(관리자)이고 idx값이 있으면
            $params[':level'] = $marr['level'];
            $params[':idx'] = $marr['idx'];
            $sql .= ", level=:level";   //회원 레벨 업데이트
            $sql .= " WHERE idx=:idx";  //관리자의 idx인 사람
        } else {    //관리자가 아니면
            $params[':id'] = $marr['id'];
            $sql .= " WHERE id=:id";    //id인 사람
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

    }

    //회원목록
    public function list($page, $limit, $paramArr) {    //paramArr : 검색 조건을 담고 있는 배열
        $start = ($page - 1) * $limit;

        $where = "";
        if($paramArr['sn'] != '' && $paramArr['sf'] != '') {
            switch($paramArr['sn']) {
                case 1 : $sn_str = 'name'; break;
                case 2 : $sn_str = 'id'; break;
                case 3 : $sn_str = 'email'; break;
            }

            $where = " WHERE ". $sn_str."=:sf ";
        }

        $sql = "SELECT idx, id, name, email, DATE_FORMAT(create_at, '%Y-%m-%d %H:%i') AS create_at
        FROM member ". $where ."
        ORDER BY idx DESC LIMIT ". $start .",". $limit;

        $stmt = $this->conn->prepare($sql);

        if($where != ''){   //sn, sf 깂이 있음
            $stmt->bindParam(':sf', $paramArr['sf']);
        }

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function total($paramArr) {

        $where = "";
        if($paramArr['sn'] != '' && $paramArr['sf'] != '') {
            switch($paramArr['sn']) {
                case 1 : $sn_str = 'name'; break;
                case 2 : $sn_str = 'id'; break;
                case 3 : $sn_str = 'email'; break;
            }

            $where = " WHERE ". $sn_str."=:sf ";
        }

        $sql = "SELECT COUNT(*) cnt FROM member ". $where;
        $stmt = $this->conn->prepare($sql);
        if($where != ''){
            $stmt->bindParam(':sf', $paramArr['sf']);
        }

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['cnt'];
    }

    //회원목록 데이터
    public function getAllData() {
        $sql = "SELECT * FROM member ORDER BY idx ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);  //패치모드 설정 : 회원 목록 연관배열로 반환. 즉, 결과 셋의 각 행이 컬럼 이름을 키로 하는 배열로 반환
        $stmt->execute();
        return $stmt->fetchAll();
    }

    //회원삭제
    public function member_del($idx){
        $sql = "DELETE FROM member WHERE idx=:idx";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idx', $idx);
        $stmt->execute();
    }

    //프로필 이미지 업로드
    public function profile_upload($id, $new_photo, $old_photo = '') {
        if($old_photo != ''){
            unlink(PROFILE_DIR.$old_photo); //기존 사진 삭제
        }
    
        $tmparr = explode('.', $new_photo['name']);     //'.'기준으로 사진 원래 이름 나누기
        $ext = end($tmparr);    //사진 원래 이름으로부터 확장자 추출
        $photo = $id.'.'.$ext;  //id.[확장자]로 사진이름 설정
    
        copy($new_photo['tmp_name'], PROFILE_DIR."/".$photo);
    
        return $photo;
    }
}