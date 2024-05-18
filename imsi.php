<?php
    //db 연결
    include 'inc/dbconfig.php';
    include 'inc/member.php';

    $id = 'kingchobo';

    $mem = new Member($db);

    if($mem->id_exists($id)){
        echo "id 중복됩니다.";
    } else {
        echo "사용할 수 있는 id입니다.";
    }
?>