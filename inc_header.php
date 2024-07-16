<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($g_title) && $g_title != '') ? $g_title : '순시큐리티'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php
if(isset($js_array)) {    //각 게시판의 동작(읽기, 쓰기, 수정에 대한 로직 js 코드)
  foreach($js_array AS $var){
    echo '<script src="'.$var.'?v='.date('YmdHis').'"></script>'.PHP_EOL;
  }
}
?>
    <script src="js/member.js"></script>

</head>
<body>
    <div class="container">
        <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
              <img src="images/logo.svg" style="width: 2rem;" class="me-2">
              <span class="fs-4">순시큐리티</span>
            </a>

            <ul class="nav nav-pills">

      <?php
      //로그인 상태 체크
      if(isset($ses_id) && $ses_id != '') {
      ?>
        <li class="nav-item"><a href="index.php" class="nav-link<?= ($menu_code == 'home') ? 'active': ''; ?>">Home</a></li>
        <li class="nav-item"><a href="company.php" class="nav-link <?= ($menu_code == 'company') ? 'active': ''; ?>">회사소개</a></li>
        <?php
        if($ses_level == 10) {    //레벨 10(관리자 레벨)이면 관리자 페이지로
        ?>
        <li class="nav-item"><a href="./admin/" class="nav-link <?= ($menu_code == 'member') ? 'active': ''; ?>">Admin</a></li>
        <?php
        } else {
        ?>  <!--관리자가 아니면 마이페이지로-->
        <li class="nav-item"><a href="mypage.php" class="nav-link <?= ($menu_code == 'member') ? 'active': ''; ?>">My Page</a></li>
      <?php
      }
      ?>
      <?php
        foreach($boardArr AS $row) {
          echo '<li class="nav-item"><a href="board.php?bcode='.$row['bcode'].'"class="nav-link';
          if(isset($_GET['bcode']) && $_GET['bcode'] == $row['bcode']) {    //게시판 코드 확인
            echo ' active';   //현재 있는 게시판이 파란색으로 표시됨
          }
          echo '">'.$row['name'].'</a></li>';   //게시판 이름 출력 + 링크
        }
      ?>



      <li class="nav-item"><a href="./pg/logout.php" class="nav-link <?= ($menu_code == 'login') ? 'active': ''; ?>">로그아웃</a></li>
      <?php
        } else {
          //로그인이 안 된 상태
      ?>
        <li class="nav-item"><a href="index.php" class="nav-link<?= ($menu_code == 'home') ? 'active': ''; ?>">Home</a></li>
        <li class="nav-item"><a href="company.php" class="nav-link <?= ($menu_code == 'company') ? 'active': ''; ?>">회사소개</a></li>
        <li class="nav-item"><a href="stipulation.php" class="nav-link <?= ($menu_code == 'member') ? 'active': ''; ?>">My page</a></li>
        <li class="nav-item"><a href="board.php" class="nav-link <?= ($menu_code == 'board') ? 'active': ''; ?>">게시판</a></li>
        <li class="nav-item"><a href="login.php" class="nav-link <?= ($menu_code == 'login') ? 'active': ''; ?>">로그인</a></li>
      <?php
        }
      ?>
          </ul>
        </header>