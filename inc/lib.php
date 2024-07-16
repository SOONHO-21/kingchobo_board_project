<?php
// 게시판 페이징을 위한 코드
function my_pagination($total, $limit, $page_limit, $page, $param) {
    $total_page = ceil($total / $limit);

    //좌측 하단에 표시되기 시작하는 페이지의 번호
    $start_page = ( ( floor( ($page - 1 ) / $page_limit ) ) * $page_limit ) + 1;    //10페이지 까지 있는데 limit이 5면 6페이지부턴 6~10까지 표시
    $end_page = $start_page + $page_limit - 1;

    if($end_page > $total_page) {
        $end_page = $total_page;
    }

    $prev_page = $start_page - 1;   //이전 페이지

    if($prev_page < 1) {
        $prev_page = 1;
    }

    $rs_str = '<nav>
    <ul class="pagination">';

    $rs_str .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page=1'.$param.'">First</a></li>';

    if($prev_page > 1) {    //이전 페이지가 1페이지 이상이면
        $rs_str .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$prev_page.$param.'">Prev</a></li>';
    }

    for($i = $start_page; $i <= $end_page; $i++){
        if($i == $page) {
            $rs_str .= "<li class=\"page-item active\">
            <a class=\"page-link\" href=\"#\">{$i}</a> 
            </li>";     //페이지 리스트 및 링크

        } else {
            $rs_str .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF']."?page={$i}{$param}\">{$i}</a></li>";
        }
    }

    $next_page = $end_page + 1;
    if($next_page <= $total_page) {
        $rs_str .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF']."?page={$next_page}{$param}\">Next</a></li>";
    }

    if($page < $total_page) {
        $rs_str .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF']."?page={$total_page}{$param}\">Last</a></li>";
    }
    
    $rs_str .= '</ul></nav>';

    return $rs_str;
}
?>