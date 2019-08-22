<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


/**
 * <스타일북 좋아요 한 사람 불러오기>
 * 클라이언트에서 글id를 가져온다.
 * 좋아요 테이블에서 글id에 해당하는 글의 개수를 찾는다.
 * 결과를 response 한다.
 */

if (isset($_POST['sb_id'])) {
    $sb_id = $_POST['sb_id'];
//    echo "sb_id".$sb_id;
}

if (isset($sb_id)) {

//    $sql = "SELECT COUNT(*) FROM sb_like_list WHERE sb_id='$sb_id'";
//    $result = mysqli_query($connect, $sql);
//    $response = $result;
//    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    $sql = "SELECT * FROM sb_like_list WHERE sb_id='$sb_id'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);
    $response = $count;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} else {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

mysqli_close($connect);

?>





