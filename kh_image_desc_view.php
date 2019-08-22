<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['no'])) { //메인화면에, 좋아요때문에 그런거같은데 흠 ..
    $no = $_POST['no'];
}

if (isset($_POST['order'])) { //메인화면에, 좋아요때문에 그런거같은데 흠 ..
    $order = $_POST['order'];
}

if (isset($_POST['id'])) { //메인화면에, 좋아요때문에 그런거같은데 흠 ..
    $id = $_POST['id'];
}

if (isset($no) and empty($order)) { //$no값만 왔을 때, 전체 이미지 개수 파악.
//글id를 가져와야댐..
    $sql2 = "SELECT * FROM knowhow_list";
    $result2 = mysqli_query($connect, $sql2);
    $count2 = mysqli_num_rows($result2);
    if ($count2 > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            $kh_id = $row['id'];
            $sql = "SELECT * FROM kh_image_desc_list WHERE kh_id='$kh_id' ORDER BY `order` ASC";
            $result = mysqli_query($connect, $sql);
            $count3 = mysqli_num_rows($result);

            if ($count3 > 0) {
                $response = array();
                $response['message'] = "$count3";
                echo json_encode($response);
            } else {
                echo "데이터가 없습니다.";
            }
        }
    }
} elseif (isset($id) and isset($order)) { //$id, $order 둘 다 있을때, 이미지설명 반환.
    $sql = "SELECT * FROM kh_image_desc_list WHERE kh_id='$id' and `order`='$order'";
    $result = mysqli_query($connect, $sql);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        $row = mysqli_fetch_assoc($result);
        $response = array();
        $response['image_desc'] = $row['image_desc'];

        echo json_encode($response);
    } else {
        echo "데이터가 없습니다.";
    }
}
$connect->close();

?>