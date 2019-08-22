<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


/**
 * <스타일북 좋아요 한 사람 불러오기>
 * 클라이언트에서 글id를 가져온다.
 * 좋아요 테이블에서 글id에 해당하는 글을 모두 찾는다.
 * array에 저장하여 클라이언트로 response 한다.
 */

if (isset($_POST['sb_id'])) {
    $sb_id = $_POST['sb_id'];
//    echo "sb_id".$sb_id;
}

if (isset($_POST['no'])) { //내회원정보no
    $login_id = $_POST['no'];
//    echo "sb_id".$sb_id;
}

if (isset($sb_id)) {

    $sql = "SELECT m.no, m.name, m.email, m.picture
        FROM sb_like_list AS l
        JOIN member_info AS m
        ON l.liker_id = m.no
        WHERE l.sb_id ='$sb_id'
        ORDER BY l.regdate ASC;";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {

        $likes_array = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $response = array();
            $liker_id = $row['no'];
            $response['name'] = $row['name'];
            $response['email'] = $row['email'];
            $response['picture'] = $row['picture'];

            $sql1 = "SELECT * FROM follow_list WHERE following_id='$login_id' and follow_id='$liker_id'";
            $result1 = mysqli_query($connect, $sql1);

            if ($result1) {//팔로우 받은사람 = 좋아요한사람, 팔로우한사람 = 로그인한사람 일경우, "팔로잉"반환

                $response['follow'] = "팔로잉";

            } else {
                if ($result2) {//팔로받은사람 = 로그인한사람, 팔로우한사람 = 좋아요한사람 일 경우, "팔로우 반환"
                    $sql2 = "SELECT * FROM follow_list WHERE following_id='$liker_id' and follow_id='$login_id'";
                    $result2 = mysqli_query($connect, $sql2);
                    $row = mysqli_fetch_assoc($result2);
                    $response['follow'] = "팔로우";

                } else {
                    $response['follow'] = "팔로우";
                }
            }
            array_push($likes_array, $response);
        }
        echo json_encode($likes_array);

    } else {
        echo "데이터가 없습니다.";
    }
}else{
    echo "데이터가 없습니다.";
}


mysqli_close($connect);

?>





