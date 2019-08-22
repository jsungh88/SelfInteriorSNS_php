<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['sb_id'])) {
    $sb_id = $_POST['sb_id'];

}
if (isset($_POST['liker_id'])) {
    $liker_id = $_POST['liker_id'];

}

if(isset($sb_id) and isset($liker_id)){
    $sql = "SELECT * FROM sb_like_list WHERE `sb_id`='$sb_id' and `liker_id`='$liker_id'";
    $result = mysqli_query($connect,$sql);
    $count = mysqli_num_rows($result);
    $response = array();
    if($count==1){
        $response['sb_id'] = $sb_id;
        $response['heart'] = "true";
        echo json_encode($response);
    }



}
//접속 종료
mysqli_close($connect);
?>