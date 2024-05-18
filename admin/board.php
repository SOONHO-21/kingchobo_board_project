<?php

$g_title = '순시큐리티';
$js_array = ['js/board.js'];

$menu_code = 'board';

include 'inc_common.php';
include 'inc_header.php';
include '../inc/dbconfig.php';
include '../inc/board.php';    //게시판관리 Class
include '../inc/lib.php';       //페이지네이션

$sn = (isset($_GET['sn']) && $_GET['sn'] != '' && is_numeric($_GET['sn'])) ? $_GET['sn'] : '';
$sf = (isset($_GET['sf']) && $_GET['sf'] != '') ? $_GET['sf'] : '';

//$total, $limit, $page_limit, $page, $param

$paramArr = ['sn' => $sn, 'sf' => $sf];

$board = new Board($db);

$boardArr = $board->list();
?>
<main class="border rounded-5 p-5" style="height: calc(100vh-257px)">

    <div class="container">
        <h1>게시판관리</h1>
    </div>
    <table class="table table-border table-hover">
        <tr>
            <th>번호</th>
            <th>게시판 이름</th>
            <th>게시판 코드</th>
            <th>게시판 타입</th>
            <th>게시물 수</th>
            <th>등록일시</th>
            <th>관리</th>
        </tr>
    <?php
        foreach($boardArr AS $row) {
            //2023-11-11 11:11:11
            //$row['create_at'] = substr($row['create_at'], 0, 16);
    ?>
        <tr>
            <th><?=$row['idx']; ?></th>
            <th><?=$row['name']; ?></th>
            <th><?=$row['bcode']; ?></th>
            <th><?=$row['btype']; ?></th>
            <th><?=$row['cnt']; ?></th>
            <th><?=$row['create_at']; ?></th>
            <td>
                <button class="btn btn-primary btn-sm btn_mem_edit" data-idx="<?= $row['idx']; ?>">수정</button>
                <button class="btn btn-danger btn-sm btn_mem_delete" data-idx="<?= $row['idx']; ?>">삭제</button>
            </td>
        </tr>
    <?php
        }
    ?>
    </table>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#board_create_modal" id="btn_create_modal">게시판 생성</button>

    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="board_create_modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">게시판 생성</h1>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex gap-2">
        <input type="text" id="board_title" class="form-control" placeholder="게시판 이름">
        <select name="" id="board_type" class="form-select">
            <option value="board">게시판</option>
            <option value="gallery">갤러리</option>
        </select>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btn_board_create">확인</button>
      </div>
    </div>
  </div>
</div>

<?php
include 'inc_footer.php';

?>