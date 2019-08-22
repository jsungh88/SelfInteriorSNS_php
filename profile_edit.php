<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['no'])) {
    $no = $_POST['no'];
}
if (isset($_FILES['picture']['name'])) {
    $image = $_FILES['picture']['name'];
    $image_name = basename($image);
    $image_name = round(microtime(true) * 1000) . $image_name;
    $target_dir = "uploads/" . $image_name;
    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_dir)) {
        $msg = "이미지 업로드 성공!";
    } else {
        $msg = "이미지 업로드 실패!";
    }
}


$sql = "UPDATE member_info SET picture='$image_name' WHERE no='$no'";
$result = mysqli_query($connect, $sql);

$response = array();
$sql1 = "SELECT * FROM member_info WHERE no='$no'";
$result1 = mysqli_query($connect, $sql1);
$row = mysqli_fetch_array($result1);
$response['no'] = $row['no'];
$response['name'] = $row['name'];
$response['email'] = $row['email'];
$response['pw'] = $row['pw'];
$response['picture'] = $row['picture'];
$response['gender'] = $row['gender'];
$response['agerange'] = $row['age_range'];
$response['level'] = $row['level'];
$response['regdate'] = $row['regdate'];
$response['join_type'] = $row['join_type'];
echo json_encode($response);


//접속 종료
mysqli_close($connect);

?>