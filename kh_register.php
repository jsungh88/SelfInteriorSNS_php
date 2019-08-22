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
    echo "$section" . "/";
}

if (isset($_POST['style'])) {
    $style = $_POST['style'];
    echo "$style" . "/";
}
if (isset($_POST['space'])) {
    $space = $_POST['space'];
    echo "$space" . "/";
}
if (isset($_POST['subject'])) {
    $subject = $_POST['subject'];
    echo "$subject" . "/";
}
if (isset($_POST['desc'])) {
    $desc = $_POST['desc'];
    echo "$desc" . "/";
}
if (isset($_POST['tags'])) {
    $tag = $_POST['tags'];
    echo "$tag" . "/";
}
if (isset($_POST['writer'])) {
    $writer = $_POST['writer'];
    echo "$writer" . "/";
}
if (isset($_POST['writer_no'])) {
    $writer_no = $_POST['writer_no'];
    echo "$writer_no" . "/";
}
$regdate = date("Y-m-d H:i:s");

$response = array();
$sql = "INSERT INTO knowhow_list(`subject`,`category_section`,`category_style`,`category_space`,`tag`,`desc`,`regdate`,`FK_writer_no`)VALUES('$subject','$section','$style','$space','$tag','$desc','$regdate','$writer_no')";
$result = mysqli_query($connect, $sql);

$sql2 = "SELECT * FROM knowhow_list WHERE subject='$subject' and category_section='$section' and tag='$tag' and `desc`='$desc'";
$result2 = mysqli_query($connect,$sql2);
$row2 = mysqli_fetch_assoc($result2);
$id = $row2['id'];


if (isset($_FILES['picture']['name'])) {
    $image[] = $_FILES['picture']['name'];
    echo "///" . (array_values($image)) . "///";
}

if (isset($_POST['picture_desc'])) {
    $picture_desc[] = $_POST['picture_desc'];
    echo "///" . (array_values($picture_desc)) . "///";
}

for ($i = 0; $i<count($_POST["picture_desc"]); $i++) {
    $picture_desc = $_POST["picture_desc"];
    echo "$picture_desc";
    $sql4 = "INSERT INTO kh_image_desc_list(kh_id,image_desc,regdate) VALUES('$id','$picture_desc','$regdate')";
    $result4 = mysqli_query($connect,$sql4);
}



print_r ($_FILES [ 'picture']);
//$result = array("success" => "OKOK");


for ($i = 0; $i < count($_FILES ['picture'] ['name']); $i++) {

    try {
        if (move_uploaded_file($_FILES ['picture'] ["tmp_name"][$i], "uploads/".round(microtime(true) * 1000).$_FILES ["picture"] ["name"][$i])) {

            echo $msg = "이미지 업로드 성공!";
            //저장 쿼리 삽입
            $sql3 = "INSERT INTO kh_image_list(kh_id,image,regdate) VALUES('$id','$picture','$regdate')";
            $result3 = mysqli_query($connect,$sql3);

        } else {
            echo $msg = "이미지 업로드 실패!";
        }
    } catch (Exception $e) {
        die('File did not upload: ' . $e->getMessage());
    }
//    echo json_encode($result, JSON_PRETTY_PRINT);
}






$response['message']="성공";
json_encode($response,JSON_UNESCAPED_UNICODE);


//접속 종료
mysqli_close($connect);
?>