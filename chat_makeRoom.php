<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['roomName'])) { //채팅방이름
    $room_name = $_POST['roomName'];
}

if (isset($_POST['no'])) { //사용자no있으면
    $no = $_POST['no'];
}

$sql = "SELECT * FROM chatroom WHERE roomname='$room_name'";
$result = mysqli_query($connect, $sql);
//$count = mysqli_num_rows($result);

//if ($count == 0) {
    $sql1 = "INSERT INTO chatroom(`roomname`,`leader`) VALUES('$room_name','$no')";
    $result1 = mysqli_query($connect, $sql1);


    $sql2 = "SELECT * FROM chatroom WHERE roomname='$room_name' and leader='$no'";
    $result2 = mysqli_query($connect, $sql2);
    $row = mysqli_fetch_assoc($result2);

    $response = array();
    $room_id = $row['id'];
    $response['roomId'] = $row['id'];
    $response['roomName'] = $row['roomname'];

    $sql4 = "SELECT * FROM member_info WHERE no='$no'";
    $result4 = mysqli_query($connect, $sql4);
    $row1 = mysqli_fetch_assoc($result4);
    $response['leaderName'] = $row1['name']; //방장이름

    //채팅방 참여인 저장.
    $sql3 = "INSERT INTO chat_participants(`FK_room_id`,`FK_user_id`) VALUES('$room_id','$no')";
    $result3 = mysqli_query($connect, $sql3);

    echo json_encode($response);
//} else {
////    $response['error'] = "동명 채팅방이 존재합니다.";
////    echo json_encode($response);
//    echo "동명 채팅방이 존재합니다.";
//}

//접속 종료
mysqli_close($connect);

?>
