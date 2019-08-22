<?php
/**
 * Created by PhpStorm.
 * User: Joanne
 * Date: 2018-06-04
 * Time: 오후 5:15
 */

header('content-type: text/html; charset=utf-8');
//header('Content-type: application/json');
require "init.php";

if (isset($_POST['email'])) {    $email = $_POST['email'];}
if (isset($_POST['pwd'])) {    $pwd = $_POST['pwd'];}


/**
 * <로그인 절차>
 * 1. (서버) 이메일,비밀번호가 전달되었는지 확인한다.
 * 2. (서버) 해당 이메일,비밀번호의 회원정보DB 존재유무를 확인한다.
 *  2-1. (서버) 이메일이 없을 경우, ‘이메일을 다시 한번 확인해주세요’ 메시지를 보낸다.
 *  2-2. (서버) 이메일이 있을 경우, 비밀번호가 일치하는지 확인한다.
 *  2-3. (서버) 이메일이 일치하는데, 비밀번호가 일치하지 않을 경우, ‘비밀번호를 다시 한번 확인해주세요’ 문구를 전달한다.
 *  2-4. (서버) 비밀번호가 일치할 경우, 해당 회원정보를 모두 클라이언트로 전달한다.
 */


$sql = "SELECT * FROM member_info WHERE email='$email' and pw='$pwd'";
$result = mysqli_query($connect, $sql);
$count = mysqli_num_rows($result);
$response = array();

while ($row = mysqli_fetch_assoc($result)) {
//    $response[] = $row;
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
}

if ($count == 1) { //일치하는 회원이 있을 경우
    echo json_encode($response);
} else { //일치하는 회원이 없을 경우
    $remail = $row['email'];
    $rpwd = $row['pwd'];

    $str1 = strcmp($email, $remail);
    $str2 = strcmp($pwd, $rpwd);
    if ($str1 == true && $str2 == false) { //email은 일치 && pwd 는 불일치일 경우
        echo "비밀번호를 확인해주세요";
    } elseif (!$str1) { //email이 일치하지 않을 경우
        echo "등록되지 않은 이메일입니다.";
    }

    echo "입력정보를 다시 한 번 확인해주세요";
}


//접속 종료
mysqli_close($connect);

?>