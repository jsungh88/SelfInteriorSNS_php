<?php

header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


if (isset($_POST['no'])) { //유저no
    $user_no = $_POST['no'];
}

if (isset($_POST['Token'])) { //토큰
    $Token = $_POST['Token'];
}

//if(isset($user_no) and isset($Token)){
//
//    $sql = "UPDATE userToken SET `TOKEN`='$Token',`FK_user_no`='$user_no' WHERE TOKEN='$Token'";
//    $result = mysqli_query($connect,$sql);
//
//    echo "토큰 업데이트 성공";
//}else{
if (isset($user_no) and isset($Token)) {
    $sql3 = "SELECT * FROM userToken WHERE FK_user_no='$user_no'";
    $result3 = mysqli_query($connect, $sql3);
    $count = mysqli_num_rows($result3);
    if($count > 0 ){
        $sql = "UPDATE userToken SET `TOKEN`='$Token',`FK_user_no`='$user_no' WHERE FK_user_no='$user_no'";
        $result = mysqli_query($connect, $sql);
    }else{
        $sql2 ="INSERT INTO userToken(`TOKEN`,`FK_user_no`) VALUES('$Token','$user_no')";
        $result2 = mysqli_query($connect, $sql2);
    }
    echo "토큰 업데이트 성공";
} else {


    $sql1 = "INSERT INTO userToken(`TOKEN`) VALUES('$Token')";
    $result1 = mysqli_query($connect, $sql1);

    echo "토큰 저장 성공";
}

//접속 종료
mysqli_close($connect);

?>