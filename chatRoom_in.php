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

//만약 이미 등록되어있는 경우,  추가하지 않는다.
$sql1 = "SELECT * FROM chat_participants WHERE FK_room_id='$roomId' and FK_user_id='$no'";
$result1 = mysqli_query($connect, $sql1);
$count1 = mysqli_num_rows($result1);
$response = array();
if($count1==0){
    $sql = "INSERT INTO chat_participants(`FK_room_id`,`FK_user_id`,`first`) VALUES('$roomId','$no','false')";
    $result = mysqli_query($connect, $sql);

    $response['message']="성공";

}else{
    $response['error']="실패";
}
echo json_encode($response);


?>


