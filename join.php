<?php header('content-type: text/html; charset=utf-8');

require "init.php";


//header('content-type: text/html; charset=utf-8');
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
//$host = '127.0.0.1';
//$username = 'root';
//$password = 'Root1234#';
//$database = 'project';

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['name'])) {    $name = $_POST['name'];}
if (isset($_POST['email'])) {    $email = $_POST['email'];}
if (isset($_POST['pwd'])) {    $pwd = $_POST['pwd'];}
if (isset($_POST['gender'])) {    $gender = $_POST['gender'];}
if (isset($_POST['agerange'])) {    $agerange = $_POST['agerange'];}
if (isset($_FILES['picture']['name'])) {    $image = $_FILES['picture']['name'];}
$regdate = date("Y-m-d H:i:s");
$level = "3";
$join_type = "일반";

/**
 * <회원가입 절차>
 * 1. (서버) 이름,이메일,비밀번호,성별,연령대 정보가 잘 도착했는지 확인한다.
 * 2. (서버) <회원정보DB>에 같은 이메일 주소가 있는지 확인한다.
 *  2-1. (서버) 있을 경우, "이미 가입된 회원입니다" 메시지를 클라이언트로 전달한다.
 *  2-2. (서버) 없을 경우, <회원정보DB>에 이름, 이메일, 비밀번호, 성별, 연령대, 이미지명, 현재날짜, 회원등급, 가입타입 을 저장한다.
 * 3. (서버) 방금 가입한 회원정보를 클라이언트에 전달한다.
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

$sql = "SELECT * FROM member_info WHERE email='$email'";
$result = mysqli_query($connect, $sql);
$count = mysqli_num_rows($result);

if ($count > 0) {
//    $response['message']="이미 가입된 회원입니다."
    echo "이미 가입된 회원입니다.";
} else {
    $response = array();
    $sql1 = "INSERT INTO member_info(name, email, pw, picture, gender, age_range, regdate, join_type) VALUES('$name','$email','$pwd','$image_name','$gender','$agerange','$regdate','$join_type')";
    $result1 = mysqli_query($connect, $sql1);

    $sql3 = "SELECT * FROM member_info WHERE email='$email'";
    $result3 = mysqli_query($connect, $sql3);
    $row = mysqli_fetch_array($result3);
//    $response[]=$row;
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
}


//접속 종료
mysqli_close($connect);

?>