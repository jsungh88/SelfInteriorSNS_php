<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['sb_id'])) { //글ID
    $sb_id = $_POST['sb_id'];
//    echo "sb_id".$sb_id;
}
if (isset($_POST['writer_no'])) { //댓글 작성한 사람
    $writer_no = $_POST['writer_no'];
//    echo "writer_no".$writer_no;
}
if (isset($_POST['depth'])) {
    $depth = $_POST['depth'];
//    echo "depth".$depth;
}
if (isset($_POST['group'])) { //글그룹(댓글 or 대댓글)
    $group = $_POST['group'];
//    echo "group".$group;
}
if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
//    echo "comment".$comment;
}
//$dateTime = new DateTime("now");
//echo $dateTime->format("Y-m-d H:i:s")
$regdate = date("Y-m-d H:i:s");

/**
 * <댓글 등록 절차>
 * 1. (서버) 글id, 작성자no, depth, 댓글 정보가 잘 도착했는지 확인한다. (대댓글의 경우 group)
 * 2. (서버) depth = 0 일 경우 댓글, depth>0 일 경우 대댓글이다.
 *  2-1. 댓글일 경우,
 *     2-1-1. 해당 글id,depth가 있는 데이터를 찾는다.
 *     2-1-2. 순서값 = 찾은글의 개수 + 1
 *  2-2. 대댓글일 경우,
 *     2-2-1. 해당 글id,depth+1가 있는 데이터를 찾는다.
 *     2-2-2. 순서값 = 찾은글의 개수 + 1
 * 3. (서버) <댓글DB>에 해당 정보를 저장한다.
 * 4. (서버) 성공메시지를 보낸다.
 */

if (isset($_POST['sb_id']) and isset($_POST['writer_no']) and isset($_POST['depth']) and isset($_POST['comment'])) {
    if ($depth == 0) {//댓글

        $sql1 = "SELECT * FROM sb_comment_list WHERE sb_id='$sb_id' and depth='$depth'";
        $result1 = mysqli_query($connect, $sql1);
        $count1 = mysqli_num_rows($result1);
        $order = $count1 + 1;

        $sql2 = "INSERT INTO sb_comment_list(`sb_id`,`writer_id`,`order`,`depth`,`comment`,`regdate`) VALUES('$sb_id','$writer_no','$order','$depth','$comment','$regdate')";
        $result2 = mysqli_query($connect, $sql2);

        //저장한 글의 댓글 id 를 가져와서 같은 댓글 group에 등록한다. - 대댓글 소팅 구분.
        $sql6 = "SELECT * FROM sb_comment_list WHERE sb_id='$sb_id' and writer_id='$writer_no' and `order`='$order' and depth='$depth' and comment='$comment' and regdate='$regdate'";
        $result6 = mysqli_query($connect, $sql6);
        $row6 = mysqli_fetch_assoc($result6);
        $comment_id = $row6['id'];

        $sql7 = "UPDATE sb_comment_list SET `group`='$comment_id' WHERE id='$comment_id'";
        $result7 = mysqli_query($connect, $sql7);

        if ($result2 == 1) {
            ///해당 글의 댓글 모두 불러오기
            $sql5 = "SELECT c.id, c.sb_id, c.order, c.depth, c.comment, c.regdate, c.group, m.no, m.name, m.picture
    FROM sb_comment_list AS c
    JOIN member_info AS m
    ON c.writer_id = m.no
    WHERE c.sb_id = '$sb_id'
    ORDER BY `group` ASC, depth ASC, `order` ASC";
            $result5 = mysqli_query($connect, $sql5);
            $count5 = mysqli_num_rows($result5);

            if ($count5 > 0) {
                $comments_array = array();
                while ($row = mysqli_fetch_assoc($result5)) {
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
//+알림내역 테이블에 등록하기.
            //이름 불러오기
            $sql7 = "SELECT * FROM member_info WHERE no='$writer_no'";
            $result7 = mysqli_query($connect, $sql7);
            $row = mysqli_fetch_assoc($result7);
            $user_name = $row['name'];
            $noti_desc = $user_name . "님이 댓글을 남겼습니다.";

            //글쓴이no 가져오기
            $sql7 = "SELECT * FROM stylebook_list WHERE `id`='$sb_id'";
            $result7 = mysqli_query($connect, $sql7);
            $row7 = mysqli_fetch_assoc($result7);
            $user_no = $row7['FK_writer_no'];

            //글작성자와 댓글작성자가 같다면, 알림내역에 저장하지 않는다.
            $str = strcmp($writer_no,$user_no);
            if($str){//같지않다면 저장한다.
                //writer_no 보낸 사람, user_no 받는 사람
                $sql6 = "INSERT INTO notification_list(`group`,`category`,`noti_desc`,`sender_no`,`receiver_no`,`sb_id`,`regdate`) VALUES('스타일북','댓글','$noti_desc','$writer_no','$user_no','$sb_id','$regdate')";
                $result6 = mysqli_query($connect, $sql6);
            }
        }

    } elseif ($depth > 0) {//대댓글

        $sql3 = "SELECT * FROM sb_comment_list WHERE sb_id='$sb_id' and depth='$depth' and `group`='$group'";
        $result3 = mysqli_query($connect, $sql3);
        $count3 = mysqli_num_rows($result3);
        $order = $count3 + 1;

        $sql4 = "INSERT INTO sb_comment_list(`sb_id`,`writer_id`,`order`,`depth`,`comment`,`regdate`,`group`) VALUES('$sb_id','$writer_no','$order','$depth','$comment','$regdate','$group')";
        $result4 = mysqli_query($connect, $sql4);

        if ($result4 == 1) {

            ///해당 글의 댓글 모두 불러오기
            $sql5 = "SELECT c.id, c.sb_id, c.order, c.depth, c.comment, c.regdate, c.group, m.no, m.name, m.picture
    FROM sb_comment_list AS c
    JOIN member_info AS m
    ON c.writer_id = m.no
    WHERE c.sb_id = '$sb_id'
    ORDER BY `group` ASC, depth ASC, `order` ASC";
            $result5 = mysqli_query($connect, $sql5);
            $count5 = mysqli_num_rows($result5);

            if ($count5 > 0) {
                $comments_array = array();
                while ($row = mysqli_fetch_assoc($result5)) {
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
        }
        //+알림내역 테이블에 등록하기.
        //이름 불러오기
        $sql7 = "SELECT * FROM member_info WHERE no='$writer_no'";
        $result7 = mysqli_query($connect, $sql7);
        $row = mysqli_fetch_assoc($result7);
        $user_name = $row['name'];
        $noti_desc = $user_name . "님이 대댓글을 남겼습니다.";

        //글쓴이no 가져오기
        $sql7 = "SELECT * FROM stylebook_list WHERE `id`='$sb_id'";
        $result7 = mysqli_query($connect, $sql7);
        $row7 = mysqli_fetch_assoc($result7);
        $user_no = $row7['FK_writer_no'];

        //글작성자와 댓글작성자가 같다면, 알림내역에 저장하지 않는다.
        $str = strcmp($writer_no,$user_no);
        if($str){
            //writer_no 보낸 사람, user_no 받는 사람
            $sql6 = "INSERT INTO notification_list(`group`,`category`,`noti_desc`,`sender_no`,`receiver_no`,`sb_id`,`regdate`) VALUES('스타일북','댓글','$noti_desc','$writer_no','$user_no','$sb_id','$regdate')";
            $result6 = mysqli_query($connect, $sql6);
        }

    }
} else {
    $response['error'] = '댓글 등록에 실패하였습니다';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}


//접속 종료
mysqli_close($connect);

?>