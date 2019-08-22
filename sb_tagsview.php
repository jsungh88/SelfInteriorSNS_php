<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['tags'])) {
    $tags = $_POST['tags'];
}
if (isset($_POST['no'])) {
    $no = $_POST['no'];
}

/**
 * <스타일북 해시태그 리스트>
 * A. $tag 문자열(해시태그)이 포함된 행을 가져온다.
 * 1. (서버) 필요한 데이터를 불러온다.
 *   1-1. stylelist : 이미지,내용,지역
 *   1-2. member_info : 작성자 프로필, 이름
 *   1-3. sb_like_list : 좋아요 갯수
 *   1-4. sb_comment_list : 댓글 갯수
 * 2. 데이터를 array()에 담아 클라이언트로 전달한다.

 */
if (isset($tags)) { //태그값이 있는 경우 - 검색
    $sql1 = "SELECT s.id, s.image, s.content, s.location, s.location_lat, s.location_lng, s.regdate, m.no, m.name, m.picture
FROM stylebook_list AS s
JOIN member_info AS m
ON s.FK_writer_no = m.no
WHERE s.content LIKE '%$tags%'
ORDER BY s.id DESC";
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
}


$connect->close();

?>