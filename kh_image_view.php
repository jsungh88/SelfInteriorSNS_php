<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['no'])) { //글id 여야함. . .
    $no = $_POST['no'];
}

if (isset($_POST['order'])) { //메인화면에, 좋아요때문에 그런거같은데 흠 ..
    $order = $_POST['order'];
}

if (isset($no) and empty($order)) { //$no값만 왔을 때, 전체 이미지 개수 파악.
    $sql = "SELECT * FROM kh_image_list WHERE kh_id='$no' ORDER BY `order` ASC";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);

    if($count > 0){
            $response = array();
            $response['message']="$count";

        echo json_encode($response);
    }else{
        echo "데이터가 없습니다.";
    }
}elseif(isset($no) and isset($order)){ //$no, $order 둘 다 있을때, 이미지 반환.
    $sql = "SELECT * FROM kh_image_list WHERE kh_id='$no' and `order`='$order'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);
    if($count > 0){
        $row = mysqli_fetch_assoc($result);
        $response = array();
        $response['image'] = $row['image'];

        echo json_encode($knowhow_image_array);
    }else{
        echo "데이터가 없습니다.";
    }
}
$connect->close();

?>