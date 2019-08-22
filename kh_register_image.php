<?php header('content-type: text/html; charset=utf-8');

require "init.php";


$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['id'])) { //글id
    $id = $_POST['id'];
}

if (isset($_FILES['picture']['name'])) {
    $image = $_FILES['picture']['name'];
}

if (isset($_POST['picture_desc'])) {
    $picture_desc = $_POST['picture_desc'];
}

$regdate = date("Y-m-d H:i:s");

echo "$id"."$image"."$picture_desc";


$image_name = basename($image);
$image_name = round(microtime(true) * 1000) . $image_name;
$target_dir = "uploads/" . $image_name;
if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_dir)) {
    $msg = "이미지 업로드 성공!";
} else {
    $msg = "이미지 업로드 실패!";
}

$sql = "SELECT * FROM kh_image_list WHERE kh_id='$id'";
$result = mysqli_query($connect,$sql);
$count = mysqli_num_rows($result);
$order = $count + 1;

$sql3 = "INSERT INTO kh_image_list(`kh_id`,`image`,`order`,`regdate`) VALUES('$id','$image_name','$order','$regdate')";
$result3 = mysqli_query($connect,$sql3);


$sql4 = "INSERT INTO kh_image_desc_list(`kh_id`,`image_desc`,`order`,`regdate`) VALUES('$id','$picture_desc','$order','$regdate')";
$result4 = mysqli_query($connect,$sql4);

$response = array();
if ($result4) {
    $response['message'] = "성공";
} else {
    $response['error'] = "실패";
}


echo json_encode($response, JSON_UNESCAPED_UNICODE);


//접속 종료
mysqli_close($connect);
?>