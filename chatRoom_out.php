<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['roomId'])) { //채팅방ID
    $roomId = $_POST['roomId'];
}

if (isset($_POST['no'])) { //사용자no
    $no = $_POST['no'];
}

$sql = "DELETE FROM chat_participants WHERE `FK_room_id`='$roomId' and `FK_user_id`='$no'";
$result = mysqli_query($connect, $sql);

$sql1 = "DELETE FROM chat_message WHERE FK_room_id='$roomId' and userNo='$no'";
$result1 = mysqli_query($connect, $sql1);

$response = array();
if($result){
    $response['message']="성공";
}else{
    $response['error']="실패";
}
echo json_encode($response);

?>