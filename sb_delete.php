<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

/**
 * <스타일북 글 삭제 절차>
 * 1. (서버) 글id가 잘 도착했는지 확인한다.
 * 2. (서버) <스타일북DB>에서 작성자번호, 이미지, 태그 정보를 이용해 삭제하고자하는 글의 id를 찾는다.
 * 3. (서버) 해당 글을 삭제한다.
 * 4. (서버) 성공문을 날린다.
 */


if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

$sql3 = "DELETE FROM stylebook_list WHERE id='$id'";
$result3 = mysqli_query($connect, $sql3);

if ($result3 == 1) {
    $response['message'] = '글이 삭제되었습니다.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    $response['error'] = '글삭제에 실패하였습니다';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>