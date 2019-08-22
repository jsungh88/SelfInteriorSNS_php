<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
}

$regdate = date("Y-m-d H:i:s");

if (isset($_POST['id']) and isset($_POST['comment'])) {
    $sql = "UPDATE sb_comment_list SET comment='$comment' WHERE id ='$id'";
    $result = mysqli_query($connect,$sql);
    if ($result == 1) {
        $response['message'] = '댓글이 수정되었습니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        $response['error'] = '댓글 수정을 실패하였습니다';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

}

//접속 종료
mysqli_close($connect);

?>