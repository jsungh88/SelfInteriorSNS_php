<?php header('content-type: text/html; charset=utf-8');

require "init.php";


$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


/**
 * 저장프로세스
 * 1.section,style,space,subject,desc,tag,writer,writer_no,picture[],picture_desc[] 를 클라이언트로부터 받아온다.
 * 2.knowhow_list에 section,style,space,subject,desc,tag,writer,writer_no를 저장한다.
 * 3.저장한 것을 다시 불러오고 글 id를 추출한다.
 * 3.kh_image_list에 글id, picture[]를 저장한다.
 * 4.kh_image_desc_list에 글id, picture_desc를 저장한다.
 * 5.종료한다.
 */

if (isset($_POST['section'])) {
    $section = $_POST['section'];
}

if (isset($_POST['style'])) {
    $style = $_POST['style'];
}
if (isset($_POST['space'])) {
    $space = $_POST['space'];
}
if (isset($_POST['subject'])) {
    $subject = $_POST['subject'];
}
if (isset($_POST['desc'])) {
    $desc = $_POST['desc'];
}
if (isset($_POST['tags'])) {
    $tag = $_POST['tags'];
}
if (isset($_POST['writer'])) {
    $writer = $_POST['writer'];
}
if (isset($_POST['writer_no'])) {
    $writer_no = $_POST['writer_no'];
}
$regdate = date("Y-m-d H:i:s");


$sql = "INSERT INTO knowhow_list(`subject`,`category_section`,`category_style`,`category_space`,`tag`,`desc`,`regdate`,`FK_writer_no`)VALUES('$subject','$section','$style','$space','$tag','$desc','$regdate','$writer_no')";
$result = mysqli_query($connect, $sql);

$sql2 = "SELECT * FROM knowhow_list WHERE subject='$subject' and category_section='$section' and tag='$tag' and `desc`='$desc'";
$result2 = mysqli_query($connect,$sql2);
//$count = mysqli_num_rows($result2);
$row2 = mysqli_fetch_assoc($result2);
$id = $row2['id'];

$response = array();
if ($result==1) {
    $response['message'] = "$id";

} else {
    $response['error'] = "실패";

}

echo json_encode($response);



//접속 종료
mysqli_close($connect);
?>