<?php
$servername = 'localhost';  //웹 서버와 DB 서버가 한 컴퓨터에 있음을 의미
$dbuser = 'root';
$dbpassword = '';
$dbname = 'memsite';

try{
    $db = new PDO("mysql:host={$servername};dbname={$dbname}", $dbuser, $dbpassword);
    
    //Prepared Statment를 지원하지 않는 경우 DB의 기능을 사용하도록 해줌
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);    //쿼리 버퍼링 활정화
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   //pdo 객체가 에러를 처리하는 방식 정함
} catch(PDOException $e) {
    echo $e -> getMessage();
}

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].'/project/member');
define('ADMIN_DIR', DOCUMENT_ROOT.'/admin');
define('DATA_DIR', DOCUMENT_ROOT.'/data');
define('PROFILE_DIR', DATA_DIR.'/profile');
?>