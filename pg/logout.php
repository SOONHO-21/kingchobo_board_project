<?php
include '../inc/dbconfig.php';
include '../inc/member.php';

$mem = new Member($db);
$mem->logout();
?>