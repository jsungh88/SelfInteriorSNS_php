<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['no'])) { //사용자no - 마이페이지에서 요청한경우 넘어옴, 메인화면인경우 안옴.
    $no = $_POST['no'];
}

if (isset($_POST['sb_no'])) { //글id - 둘 다 넘어옴.
    $sb_no = $_POST['sb_no'];
}
/**
 * <스타일북 보기>
 * 0. 스타일북을 보내는 방법에는 메인화면,마이페이지 2가지가 있다.
 * 메인화면에는 모든 정보를 뿌려주지만, 마이페이지는 로그인한 사용자의 글만 뿌려준다.
 * 그래서 해당 코드는 2가지의 경우로 나뉜다.
 *
 * A.사용자no가 넘어오는 경우와 아닌 경우.
 * 사용자 no가 넘어오지 않은 경우 - 메인 페이지
 * 1. (서버) 필요한 데이터를 불러온다.
 *   1-1. stylelist : 이미지,내용,지역
 *   1-2. member_info : 작성자 프로필, 이름
 *   1-3. sb_like_list : 좋아요 갯수
 *   1-4. sb_comment_list : 댓글 갯수
 *
 * 2. 데이터를 array()에 담아 클라이언트로 전달한다.
 *
 * B. 사용자 no가 넘어온 경우 - 마이 페이지
 * 1. (서버) where FK_writer_no 를 준 데잍터를 불러온다.
 *   1-1. stylelist : 이미지,내용,지역
 *   1-2. member_info : 작성자 프로필, 이름
 *   1-3. sb_like_list : 좋아요 갯수
 *   1-4. sb_comment_list : 댓글 갯수
 * 2. 데이터를 array()에 담아 클라이언트로 전달한다.
 */
if (isset($no)) { //값이 있는 경우 - 마이페이지
    $sql1 = "SELECT s.id, s.image, s.content, s.location, s.location_lat, s.location_lng, s.regdate, m.no, m.name, m.picture
FROM stylebook_list AS s
JOIN member_info AS m
ON s.FK_writer_no = m.no
WHERE s.FK_writer_no = '$no'
ORDER BY id DESC";
    $result1 = mysqli_query($connect, $sql1);
    $count1 = mysqli_num_rows($result1);

    if ($count1 > 0) {

        $stylebook_array = array();
        while ($row = mysqli_fetch_assoc($result1)) {
            $response = array();

            $id = $row['id'];
            $response['no'] = $row['id'];
            $response['image'] = $row['image'];
            $response['content'] = $row['content'];
            $response['location'] = $row['location'];
            $response['location_lat'] = $row['location_lat'];
            $response['location_lng'] = $row['location_lng'];
            $response['regdate'] = $row['regdate'];
            $response['writer_no'] = $row['no'];
            $response['writer_name'] = $row['name'];
            $response['writer_image'] = $row['picture'];

            $sql2 = "SELECT * FROM sb_like_list WHERE sb_id = '$id'";
            $result2 = mysqli_query($connect, $sql2);
            $count2 = mysqli_num_rows($result2);
            $like_count = $count2;

            $sql3 = "SELECT * FROM sb_comment_list WHERE sb_id = '$id'";
            $result3 = mysqli_query($connect, $sql3);
            $count3 = mysqli_num_rows($result3);
            $comment_count = $count3;

            $response['like'] = $like_count;
            $response['comment'] = $comment_count;


            // + 좋아요 리스트를 글 id 로 소팅한 결과, 로그인유저 아이디($no)와 좋아요한사람($liker_id)이 일치하는 경우 true, 아닌경우 false 반납
            $sql8 = "SELECT * FROM sb_like_list WHERE sb_id = '$id'";
            $result8 = mysqli_query($connect, $sql8);
            $row8 = mysqli_fetch_assoc($result8);
            $liker_id = $row8['liker_id'];
            $str = strcmp($no, $liker_id);
            if ($str) { //다르다.
                $response['like_shape'] = "false";
            } else { //같다.
                $response['like_shape'] = "true";
            }
            array_push($stylebook_array, $response);
        }
        echo json_encode($stylebook_array);
    } else {
        echo "데이터가 없습니다.";
    }
} elseif (empty($no) and empty($sb_no)) { //값이 없는 경우 - 메인페이지
    $sql1 = "SELECT s.id, s.image, s.content, s.location, s.location_lat, s.location_lng, s.regdate, m.no, m.name, m.picture
FROM stylebook_list AS s
JOIN member_info AS m
ON s.FK_writer_no = m.no
ORDER BY id DESC";
    $result1 = mysqli_query($connect, $sql1);
    $count1 = mysqli_num_rows($result1);

    if ($count1 > 0) {

        $stylebook_array = array();
        while ($row = mysqli_fetch_assoc($result1)) {
            $response = array();

            $id = $row['id'];
            $response['no'] = $row['id'];
            $response['image'] = $row['image'];
            $response['content'] = $row['content'];
            $response['location'] = $row['location'];
            $response['location_lat'] = $row['location_lat'];
            $response['location_lng'] = $row['location_lng'];
            $response['regdate'] = $row['regdate'];
            $response['writer_no'] = $row['no'];
            $response['writer_name'] = $row['name'];
            $response['writer_image'] = $row['picture'];

            $sql2 = "SELECT * FROM sb_like_list WHERE sb_id = '$id'";
            $result2 = mysqli_query($connect, $sql2);
            $count2 = mysqli_num_rows($result2);
            $like_count = $count2;

            $sql3 = "SELECT * FROM sb_comment_list WHERE sb_id = '$id'";
            $result3 = mysqli_query($connect, $sql3);
            $count3 = mysqli_num_rows($result3);
            $comment_count = $count3;

            $response['like'] = $like_count;
            $response['comment'] = $comment_count;

            // + 좋아요 리스트를 글 id 로 소팅한 결과, 로그인유저 아이디($no)와 좋아요한사람($liker_id)이 일치하는 경우 true, 아닌경우 false 반납
            $sql8 = "SELECT * FROM sb_like_list WHERE sb_id = '$id'";
            $result8 = mysqli_query($connect, $sql8);
            $row8 = mysqli_fetch_assoc($result8);
            $liker_id = $row8['liker_id'];
            $str = strcmp($no, $liker_id);
            if ($str) { //다르다.
                $response['like_shape'] = "false";
            } else { //같다.
                $response['like_shape'] = "true";
            }
            array_push($stylebook_array, $response);
        }
        echo json_encode($stylebook_array);
    } else {
        echo "데이터가 없습니다.";
    }
} elseif (isset($sb_no)) { //글id만 있는 경우. - 글 상세보기
    $sql1 = "SELECT s.id, s.image, s.content, s.location, s.location_lat, s.location_lng, s.regdate, m.no, m.name, m.picture
FROM stylebook_list AS s
JOIN member_info AS m
ON s.FK_writer_no = m.no
WHERE s.id = '$sb_no'";
    $result1 = mysqli_query($connect, $sql1);
    $count1 = mysqli_num_rows($result1);
    if ($count1 > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $response = array();

            $response['no'] = $row['id'];
            $response['image'] = $row['image'];
            $response['content'] = $row['content'];
            $response['location'] = $row['location'];
            $response['location_lat'] = $row['location_lat'];
            $response['location_lng'] = $row['location_lng'];
            $response['regdate'] = $row['regdate'];
            $response['writer_no'] = $row['no'];
            $response['writer_name'] = $row['name'];
            $response['writer_image'] = $row['picture'];

            $sql2 = "SELECT * FROM sb_like_list WHERE sb_id = '$sb_no'";
            $result2 = mysqli_query($connect, $sql2);
            $count2 = mysqli_num_rows($result2);
            $like_count = $count2;

            $sql3 = "SELECT * FROM sb_comment_list WHERE sb_id = '$sb_no'";
            $result3 = mysqli_query($connect, $sql3);
            $count3 = mysqli_num_rows($result3);
            $comment_count = $count3;

            $response['like'] = $like_count;
            $response['comment'] = $comment_count;

        }
        echo json_encode($response);
    } else {
        echo "데이터가 없습니다.";
    }

}


$connect->close();

?>