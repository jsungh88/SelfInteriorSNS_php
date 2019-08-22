<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['roomId'])) { //채팅방이름
    $roomId = $_POST['roomId'];
}

if (isset($_POST['no'])) { //사용자no있으면
    $no = $_POST['no'];
}

$sql = "SELECT * FROM chat_message WHERE FK_room_id = '$roomId' and userNo ='$no' ORDER BY no asc ";
$result = mysqli_query($connect, $sql);
$chat_message = array();
while($row = mysqli_fetch_assoc($result)){
    $response = array();
    $response['who'] = $row['who'];
    $response['roomId'] = $row['FK_room_id'];
    $response['userId'] = $row['userNo'];//받는사람
    $response['sender'] = $row['sender'];//보낸사람id
    $response['userName'] = $row['userName'];//보낸사람이름
    $response['userImage'] = $row['userImage'];//보낸사람이미지
    $response['message'] = $row['message'];
    $response['time'] = $row['time'];
    array_push($chat_message,$response);
}
echo json_encode($chat_message);

?>