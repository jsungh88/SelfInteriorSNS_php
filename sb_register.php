<?php header('content-type: text/html; charset=utf-8');

require "init.php";


$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['writer'])) {
    $writer = $_POST['writer'];
}
if (isset($_POST['tags'])) {
    $tags = $_POST['tags'];
}
if (isset($_POST['location'])) {
    $location = $_POST['location'];
}
if (isset($_FILES['picture']['name'])) {
    $image = $_FILES['picture']['name'];
}


if (isset($_POST['location_lat'])) {
    $lat = $_POST['location_lat'];
}
if (isset($_POST['location_lng'])) {
    $lng = $_POST['location_lng'];
}

$regdate = date("Y-m-d H:i:s");

/**
 * <스타일북 글 등록 절차>
 * 1. (서버) 제목, 태그, 위치, 이미지 정보가 잘 도착했는지 확인한다.
 * 2. (서버) <스타일북DB>에 해당 정보를 저장한다.
 * 3. (서버) 성공메시지를 보낸다.
 */


$image_name = basename($image);
$image_name = round(microtime(true) * 1000) . $image_name;
$target_dir = "uploads/" . $image_name;
if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_dir)) {
     $msg = "이미지 업로드 성공!";
} else {
     $msg = "이미지 업로드 실패!";
}

//echo $name . "/" . $email . "/" . $pwd . "/" . $gender . "/" . $agerange;
$response = array();
$sql = "INSERT INTO stylebook_list(content,image,location, regdate, FK_writer_no, location_lat,location_lng) VALUES('$tags','$image_name','$location','$regdate','$writer','$lat','$lng')";
$result = mysqli_query($connect, $sql);

if($result == 1){
    $sql1 = "SELECT * FROM stylebook_list WHERE content='$tags'";
    $result1 = mysqli_query($connect, $sql1);
    $row = mysqli_fetch_array($result1);
    $response['id'] = $row['id'];
    $response['content'] = $row['content'];
    $response['image'] = $row['image'];
    $response['location'] = $row['location'];
    $response['location_lat'] = $row['location_lat'];
    $response['location_lng'] = $row['location_lng'];
    $response['regdate'] = $row['regdate'];
    $response['writer'] = $row['FK_writer_no'];
    echo json_encode($response);
}else{
    echo "글등록에 실패하였습니다.";
}

//접속 종료
mysqli_close($connect);

?>