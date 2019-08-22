<?php  header('content-type: text/html; charset=utf-8');

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

if (isset($_POST['follow_no'])) { //팔로우하는사람
    $follow_no = $_POST['follow_no'];
//    echo $follow_no."/";
}

if (isset($_POST['following_no'])) { //팔로잉당하는사람
    $following_no = $_POST['following_no'];
//    echo $following_no."/";
}

if (isset($_POST['regit'])) { //팔로우등록
    $regit = $_POST['regit'];
//    echo $regit."/";
}

if (isset($_POST['unfollow'])) { //팔로우취소
    $unfollow = $_POST['unfollow'];
}


$regdate = date("Y-m-d H:i:s");

if(isset($follow_no) and isset($following_no) and isset($regit)) {//팔로우 : following_id = 팔로우 받은사람, follow_id = 팔로우한사람

    $sql1 = "INSERT INTO follow_list(`following_id`,`follow_id`,`regdate`) VALUES('$following_no','$follow_no','$regdate')";
    $result1 = mysqli_query($connect, $sql1);

    //로그인 유저가, 작성자 팔로우 한 경우 "팔로잉"반환, 아닐 경우 "팔로우"반환.
    $sql = "SELECT * FROM follow_list WHERE following_id='$following_no' and follow_id='$follow_no'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        $response['message'] = "팔로잉";

        /*
         * 알림내역에 등록
         * 1. 팔로우 한 사람의 이름을 가져와서 변수에 저장한다. following_name
         * 2. 팔로잉 당한 사람의 이름을 가져와서 변수에 저장한다. follow_name
         * 3. noti_desc 를 작성한다.
         * 4. 알림내역 DB에 저장한다.
        */

        $sql4 = "SELECT * FROM member_info WHERE no='$following_no'";
        $result4 = mysqli_query($connect,$sql4);
        $row = mysqli_fetch_assoc($result4);
        $following_name = $row['name'];

        $sql5 = "SELECT * FROM member_info WHERE no='$follow_no'";
        $result5 = mysqli_query($connect,$sql5);
        $row = mysqli_fetch_assoc($result5);
        $follow_name = $row['name'];

        $noti_desc = "$follow_name"." 님이 "."$following_name" . " 님을 팔로우합니다.";

        $sql3= "INSERT INTO notification_list(`category`,`noti_desc`,`sender_no`,`receiver_no`,`regdate`) VALUES ('팔로우','$noti_desc','$follow_no','$following_no','$regdate')";
        $result3 = mysqli_query($connect, $sql3);

        //Firebase로 넘기기
        /**
         * 1. 팔로우당하는사람(following_name)의 토큰 값 가져오기
         * 2. Firebase로 넘기기
         */

        $sql6 = "SELECT * FROM userToken WHERE FK_user_no='$following_no'";
        $result6 = mysqli_query($connect, $sql6);
        $row6 = mysqli_fetch_assoc($result6);
        $token = $row6['TOKEN'];
        $tokens = array();
        $tokens[] = $token;

        $arr = array();
        $arr['title']="팔로우 요청";
        $arr['message']=$noti_desc;

        $message_status = send_fcm($tokens, $arr);
    } else {
        $response['message'] = "팔로우";
    }
    echo json_encode($response);
}elseif(isset($follow_no) and isset($following_no) and isset($unfollow)){ //언팔로우

    $sql2 = "DELETE FROM follow_list WHERE following_id='$following_no' and follow_id='$follow_no'";
    $result2 = mysqli_query($connect, $sql2);

    /*알림내역에서 삭제*/
    $sql3 = "DELETE FROM notification_list WHERE category='팔로우' and sender_no='$follow_no' and receiver_no='$following_no'";
    $result3 = mysqli_query($connect, $sql3);

    //로그인 유저가, 작성자 팔로우 한 경우 "팔로잉"반환, 아닐 경우 "팔로우"반환.
    $sql = "SELECT * FROM follow_list WHERE following_id='$following_no' and follow_id='$follow_no'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        $response['message'] = "팔로잉";

    } else {
        $response['message'] = "팔로우";
    }
    echo json_encode($response);


}else{
    $response['error'] = "데이터가 없습니다.";
    echo json_encode($response);
}



