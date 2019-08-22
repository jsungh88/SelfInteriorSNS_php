<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
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

if (isset($_POST['writer'])) {
    $writer = $_POST['writer'];
}


if (isset($_POST['id']) and isset($_POST['tags']) and isset($_FILES['picture']['name'])) {
    /**
     * <스타일북 글 수정 절차>
     * 1. (서버) 글id, 이미지, 태그, 지역 정보,작성자번호가 잘 도착했는지 확인한다.
     * 2. (서버) <스타일북DB>에서 작성자번호, 이미지, 태그 정보를 이용해 수정하고자하는 글의 id를 찾는다.
     * 3. (서버) 해당 글을 수정한다.
     * 4. (서버) 성공문을 날린다.
     */
    if (isset($_FILES['picture']['name'])) {
        $image_name = basename($image);
        $image_name = round(microtime(true) * 1000) . $image_name;
        $target_dir = "uploads/" . $image_name;
        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_dir)) {
            $msg = "이미지 업로드 성공!";
        } else {
            $msg = "이미지 업로드 실패!";
        }
    }
    $sql1 = "UPDATE stylebook_list SET content='$tags',image='$image_name',location='$location',location_lat='$lat',location_lng='$lng', regdate='$regdate' WHERE id='$id'";
    $result1 = mysqli_query($connect, $sql1);
    if ($result1 == 1) {
        $response['message'] = '글이 수정되었습니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        $response['error'] = '글수정에 실패하였습니다';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}else{

    $sql2 = "UPDATE stylebook_list SET content='$tags',location='$location',location_lat='$lat',location_lng='$lng', regdate='$regdate' WHERE id='$id'";
    $result2 = mysqli_query($connect, $sql2);
    if ($result2 == 1) {
        $response['message'] = '글이 수정되었습니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        $response['error'] = '글수정에 실패하였습니다';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}

//접속 종료
mysqli_close($connect);

?>