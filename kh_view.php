<?php  header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['no'])) { //메인화면에, 좋아요때문에 그런거같은데 흠 ..
    $no = $_POST['no'];
}

if (isset($no)) {
    $sql = "SELECT k.id, k.subject, k.category_section, k.category_style, k.category_space, k.tag, k.desc, k.regdate, m.no, m.name, m.picture
     FROM knowhow_list AS k
     JOIN member_info AS m
     ON k.FK_writer_no = m.no
     ORDER BY k.id DESC";

    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if($count > 0){
        $knowhow_array = array();
        while($row = mysqli_fetch_assoc($result)){
            $response = array();

            $id = $row['id'];
            $response['id'] = $row['id'];
            $response['subject'] = $row['subject'];
            $response['section'] = $row['category_section'];
            $response['style'] = $row['category_style'];
            $response['space'] = $row['category_space'];
            $response['tag'] = $row['tag'];
            $response['desc'] = $row['desc'];
            $response['regdate'] = $row['regdate'];
            $response['writer_image'] = $row['picture'];
            $response['writer_no'] = $row['no'];
            $response['writer'] = $row['name'];

            $sql2 = "SELECT * FROM kh_like_list WHERE kh_id = '$id'";
            $result2 = mysqli_query($connect, $sql2);
            $count2 = mysqli_num_rows($result2);
            $like_count = $count2;

            $sql3 = "SELECT * FROM kh_comment_list WHERE kh_id = '$id'";
            $result3 = mysqli_query($connect, $sql3);
            $count3 = mysqli_num_rows($result3);
            $comment_count = $count3;

            $response['like_count'] = $like_count;
            $response['comment_count'] = $comment_count;

            //이미지
            $sql4 = "SELECT * FROM kh_image_list WHERE kh_id='$id' and `order`='1'";
            $result4 = mysqli_query($connect, $sql4);
            $row4 = mysqli_fetch_assoc($result4);
            $response['image']=$row4['image'];


            //이미지설명
//            $sql5 = "SELECT * FROM kh_image_desc_list WHERE kh_id='$id'";
//            $result5 = mysqli_query($connect, $sql5);
//            $row5 = mysqli_fetch_assoc($result5);
//            $response['picture_desc']=$row5['image_desc'];

            // + 좋아요 리스트를 글 id 로 소팅한 결과, 로그인유저 아이디($no)와 좋아요한사람($liker_id)이 일치하는 경우 true, 아닌경우 false 반납
            $sql8 = "SELECT * FROM kh_like_list WHERE kh_id = '$id'";
            $result8 = mysqli_query($connect, $sql8);
            $row8 = mysqli_fetch_assoc($result8);
            $liker_id = $row8['liker_id'];
            $str = strcmp($no, $liker_id);
            if ($str) { //다르다.
                $response['like_shape'] = "false";
            } else { //같다.
                $response['like_shape'] = "true";
            }
            array_push($knowhow_array, $response);
        }
        echo json_encode($knowhow_array);
    } else {
        echo "데이터가 없습니다.";
    }
}
$connect->close();

?>