<?php header('content-type: text/html; charset=utf-8');

require "init.php";

function send_fcm($token, $message)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $apiKey = "FCM_API_KEY";

    $fields = array('registration_ids'=> $token, 'data'=>$message);
    $headers = array('Authorization:key='.$apiKey,'Content-Type: application/json');

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec ( $ch );
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        echo "실패";
    }
    curl_close ( $ch );
    return $result;
}

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


if (isset($_POST['sb_id'])) {
    $sb_id = $_POST['sb_id'];

}
if (isset($_POST['liker_id'])) {
    $liker_id = $_POST['liker_id'];

}
if (isset($_POST['writer_no'])) {
    $writer_no = $_POST['writer_no'];

}

$regdate = date("Y-m-d H:i:s");

/**
 * <좋아요 등록 절차>
 * 1. 글id, 글작성자no, 좋아요한사람id 가 도착했는지 확인한다.
 * 2. 좋아요 DB에 해당 내용을 저장한다.
 *  _추가: 알림내역 저장.
 * 3. 클라이언트에 성공 메시지를 보낸다.
 * 4. 저장에 실패했을 경우, 실패 메시지를 보낸다.
 *
 *
 *
 * <좋아요 취소 절차>
 * 1. 글id, 좋아요한사람id가 도착했는지 확인한다.
 * 2. 조건을 충족하는 해당 데이터가 있는지 확인한다.
 * 3. 있을 경우, id를 추출한다.
 * 4. 해당 id 데이터를 삭제한다.
 *
 *
 */

//echo "sb_id".$sb_id;
//echo "liker_id".$liker_id;
//echo "writer_no".$writer_no;

if (isset($sb_id) and isset($writer_no) and isset($liker_id)) {//좋아요등록

    $sql1 = "INSERT INTO sb_like_list(`sb_id`,`liker_id`,`writer_id`,`regdate`) VALUES ('$sb_id','$liker_id','$writer_no','$regdate')";
    $result1 = mysqli_query($connect, $sql1);

    $sql3 = "SELECT * FROM sb_like_list WHERE `sb_id`='$sb_id'";
    $result3 = mysqli_query($connect, $sql3);
    $count1 = mysqli_num_rows($result3);

    if ($result1 == 1) {
        $response = $count1;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);

        //+알림내역 테이블에 등록하기.
        $sql7 = "SELECT * FROM member_info WHERE no='$liker_id'";
        $result7 = mysqli_query($connect, $sql7);
        $row = mysqli_fetch_assoc($result7);
        $user_name = $row['name'];

        $noti_desc = $user_name . "님이 회원님의 글을 좋아합니다.";
        $sql6 = "INSERT INTO notification_list(`group`,`category`,`noti_desc`,`sender_no`,`receiver_no`,`sb_id`,`regdate`) VALUES('스타일북','좋아요','$noti_desc','$liker_id','$writer_no','$sb_id','$regdate')";
        $result6 = mysqli_query($connect, $sql6);

        //Firebase로 넘기기
        /**
         * 1. 팔로우당하는사람(following_name)의 토큰 값 가져오기
         * 2. Firebase로 넘기기
         */

        $sql7 = "SELECT * FROM userToken WHERE FK_user_no='$writer_no'";
        $result7 = mysqli_query($connect, $sql7);
        $row7 = mysqli_fetch_assoc($result7);
        $token = $row7['TOKEN'];
        $tokens = array();
        $tokens[] = $token;

        $arr = array();
        $arr['like']="좋아요";
        $arr['message']=$noti_desc;

        $message_status = send_fcm($tokens, $arr);


    } else {
        $response = 0;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

} elseif (isset($sb_id) and !isset($writer_no) and isset($liker_id)) {//좋아요 취소

    //좋아요id 추출
    $sql2 = "SELECT * FROM sb_like_list WHERE sb_id='$sb_id' and liker_id='$liker_id'";
    $result2 = mysqli_query($connect, $sql2);
    $row1 = mysqli_fetch_assoc($result2);
    $id = $row1['id'];
//    echo $id;

    $sql4 = "DELETE FROM sb_like_list WHERE id='$id'";
    $result4 = mysqli_query($connect, $sql4);

    if ($result4 == 1) {
        $sql5 = "SELECT * FROM sb_like_list WHERE `sb_id`='$sb_id'";
        $result5 = mysqli_query($connect, $sql5);
        $count2 = mysqli_num_rows($result5);

        $response = $count2;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        $response = 0;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}

mysqli_close($connect);
?>