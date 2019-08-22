<?php  header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['login_no'])) { //로그인유저no
    $login_id = $_POST['login_no'];
}

if (isset($_POST['writer_no'])) { //작성자유저no
    $writer_id = $_POST['writer_no'];
}

if(isset($login_id) and isset($writer_id)) {//following_id = 팔로우 받은사람, follow_id = 팔로우한사람

    //로그인 유저가, 작성자 팔로우 한 경우 "팔로잉"반환, 아닐 경우 "팔로우"반환.
    $sql = "SELECT * FROM follow_list WHERE following_id='$writer_id' and follow_id='$login_id'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if($count>0) {
        $response['message'] = "팔로잉";
    }else {
        $response['message'] = "팔로우";
    }
    echo json_encode($response);
}else{
    $response['error'] = "데이터가 없습니다.";
    echo json_encode($response);
}



