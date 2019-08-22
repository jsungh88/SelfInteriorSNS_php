<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['id'])) {//댓글 id
    $id = $_POST['id'];
}

if (isset($_POST['sb_id'])) { //글id
    $sb_id = $_POST['sb_id'];
}

$sql = "DELETE FROM sb_comment_list WHERE id='$id'";
$result = mysqli_query($connect, $sql);

//알림내역의 해당 댓글도 함께 삭제.
$sql2 = "DELETE FROM notification_list WHERE id='$id' and sb_id='$sb_id'";
$result2 = mysqli_query($connect, $sql2);

$sql1 = "SELECT c.id, c.sb_id, c.order, c.depth, c.comment, c.regdate, c.group, m.no, m.name, m.picture
    FROM sb_comment_list AS c
    JOIN member_info AS m
    ON c.writer_id = m.no
    WHERE c.sb_id = '$sb_id'
    ORDER BY `group` ASC, depth ASC, `order` ASC";
$result1 = mysqli_query($connect, $sql1);
$count1 = mysqli_num_rows($result1);

if ($count1 > 0) {
    $comments_array = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $response = array();
        $response['id'] = $row['id'];
        $response['sb_id'] = $row['sb_id'];
        $response['order'] = $row['order'];
        $response['depth'] = $row['depth'];
        $response['comment'] = $row['comment'];
        $response['regdate'] = $row['regdate'];
        $response['group'] = $row['group'];
        $response['writer_no'] = $row['no'];
        $response['writer_id'] = $row['name'];
        $response['writer_image'] = $row['picture'];
        array_push($comments_array, $response);
    }
    echo json_encode($comments_array);
} else {
    echo "데이터가 없습니다.";
}

//접속 종료
mysqli_close($connect);


//if ($result == 1) {
//    $response['message'] = '댓글이 삭제되었습니다.';
//    echo json_encode($response, JSON_UNESCAPED_UNICODE);
//} else {
//    $response['error'] = '댓글삭제에 실패하였습니다';
//    echo json_encode($response, JSON_UNESCAPED_UNICODE);
//}

?>