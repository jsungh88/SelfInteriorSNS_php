<?php header('content-type: text/html; charset=utf-8');
//회원정보를 반환해준다.


require "init.php";

if (isset($_POST['no'])) {    $no = $_POST['no'];}

if(isset($no)){

    $response = array();
    $sql = "SELECT * FROM member_info WHERE no='$no'";
    $result = mysqli_query($connect, $sql);
    $row = mysqli_fetch_array($result);
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

mysqli_close($connect);

?>