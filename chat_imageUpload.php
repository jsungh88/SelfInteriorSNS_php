<?php header('content-type: text/html; charset=utf-8');

require "init.php";


$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['roomId'])) { //글id
    $roomId = $_POST['roomId'];
}

if (isset($_FILES['picture']['name'])) {
    $image = $_FILES['picture']['name'];
}

$regdate = date("Y-m-d H:i:s");



$image_name = basename($image);
$image_name = round(microtime(true) * 1000) . $image_name;
$target_dir = "uploads/" . $image_name;
if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_dir)) {
    $msg = "이미지 업로드 성공!";
} else {
    $msg = "이미지 업로드 실패!";
}

$sql = "SELECT * FROM chat_imageUpload WHERE FK_room_id='$roomId'";
$result = mysqli_query($connect,$sql);
$count = mysqli_num_rows($result);
$order = $count + 1;

$sql3 = "INSERT INTO chat_imageUpload(`FK_room_id`,`image`,`order`,`regdate`) VALUES('$roomId','$image_name','$order','$regdate')";
$result3 = mysqli_query($connect,$sql3);


$response = array();
if ($result3) {
    $response['message'] = "$image_name";
} else {
    $response['message'] = "$image_name";
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);


//접속 종료
mysqli_close($connect);
?>
