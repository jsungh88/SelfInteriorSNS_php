<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['kh_id'])) { //글id 여야함. . .
    $kh_id = $_POST['kh_id'];
}
if (isset($_POST['order'])) { //글id 여야함. . .
    $order = $_POST['order'];
}

if (isset($kh_id) and isset($order)) {
    $sql = "SELECT * FROM kh_image_list WHERE kh_id='$kh_id' and `order`='$order'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        $row = mysqli_fetch_assoc($result);
        $response = array();
        $response['image'] = $row['image'];

        $sql1 = "SELECT * FROM kh_image_desc_list WHERE kh_id='$kh_id' and `order`='$order'";
        $result1 = mysqli_query($connect, $sql1);
        $count1 = mysqli_num_rows($result1);
        $row1 = mysqli_fetch_assoc($result1);
        if ($count1 > 0) {
            $response['image_desc'] = $row1['image_desc'];
        } else {
            $response['image_desc'] = "null";
        }
        echo json_encode($response);
    } else {
        echo "데이터가 없습니다.";
    }

} elseif (isset($kh_id) and empty($order)) { //글 갯수 반환.
    //글id를 가져와야댐..
    $sql2 = "SELECT * FROM kh_image_list WHERE kh_id='$kh_id'";
    $result2 = mysqli_query($connect, $sql2);
    $count2 = mysqli_num_rows($result2);
    if ($count2 > 0) {
        $response = array();
        $response['message'] = "$count2";
        echo json_encode($response);
    }

}


