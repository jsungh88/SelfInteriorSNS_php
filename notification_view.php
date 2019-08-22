<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');


/**
 * <알림내역 보기>
 * 알림내역 종류: 좋아요, 댓글, 팔로우
 * 0. 로그인유저 no를 받는다.
 * 1. 알림내역DB를 불러온다.
 * 2-1 결과값이 있으면, 데이터를 클라이언트로 보낸다.
 * 2-2 결과값이 없으면, "데이터가 없다는"메시지를 보낸다.
 **/

if (isset($_POST['no'])) {
    $no = $_POST['no'];
}

if (isset($no)) { //값이 있는 경우
    $sql1 = "SELECT * FROM notification_list WHERE receiver_no='$no' ORDER BY regdate DESC";
    $result1 = mysqli_query($connect, $sql1);
    $count1 = mysqli_num_rows($result1);

    //1)좋아요 또는 댓글인 경우,
    //노티+스타일북 더해서 노티+글이미지 불러오고
    //member_info에서 노티센더로 이미지 찾아온다.

    //2)팔로우인경우
    //노티 전부 불러오고
    //노티+팔로우 더해서 팔로우/팔로잉여부 불러오고
    //센더아이디로 member_info 에서 담아서 보낸다.

    if ($count1 > 0) {
        $notification_array = array();
        while ($row = mysqli_fetch_assoc($result1)) {
            $id = $row['id'];
            if (isset($row['sb_id'])) { //글id가 있으면 > 좋아요, 또는 댓글이기 때문에, 글 사진이 필요함.
                $sb_id = $row['sb_id'];

                //노티+스타일북+회원정보 join해서 노티모든정보+스타일북이미지+회원이름,회원이미지 가져오기.
                $sql2 = "SELECT n.id, n.group, n.category, n.noti_desc, n.sender_no, n.receiver_no, n.sb_id, n.regdate, s.image, m.name, m.picture
FROM notification_list n, stylebook_list AS s, member_info AS m
WHERE n.sb_id = s.id
and n.sender_no = m.no
and n.sb_id = '$sb_id'
and n.receiver_no='$no'
and n.id = '$id'";
                $result2 = mysqli_query($connect, $sql2);
                while($row1 = mysqli_fetch_assoc($result2)) {
                    $response = array();
                    $response['id'] = $row1['id'];
                    $response['group'] = $row1['group'];
                    $response['category'] = $row1['category'];
                    $response['noti_desc'] = $row1['noti_desc'];
                    $response['sender_no'] = $row1['sender_no'];
                    $response['receiver_no'] = $row1['receiver_no'];
                    $response['sb_id'] = $row1['sb_id'];
                    $response['regdate'] = $row1['regdate'];
                    $response['image'] = $row1['image'];
                    $response['user_name'] = $row1['name'];
                    $response['user_image'] = $row1['picture'];
                    $response['follow'] = "null";
                    array_push($notification_array, $response);
                }
            } else { //글id가 없으면, 팔로우이다. 이때는 팔로우인지, 팔로워인지 판단 필요함.
                $sender_no = $row['sender_no'];
                $receiver_no = $row['receiver_no'];

                $sql3 = "SELECT * FROM follow_list WHERE following_id='$sender_no' and follow_id='$receiver_no'"; //팔로잉한사람 = 보낸사람, 팔로우받은사람 = 받은사람
                $result3 = mysqli_query($connect, $sql3);
                $count3 = mysqli_num_rows($result3);

                if($count3>0) {
                    $follow = "팔로잉";
                }else{
                    $follow = "팔로우";
                }

//                if ($result3) {//팔로우 받은사람 = 글을좋아요한사람, 팔로우한사람 = 로그인한사람 인데, 반대의경우(즉,팔로우받은사람 = 로그인한사람, 팔로우한사람 = 글을좋아요한사람) 인 경우가 없는 경우 팔로우, 있는경우 팔로잉
//                    $sql4 = "SELECT * FROM follow_list WHERE following_id='$receiver_no' and follow_id='$sender_no'";
//                    $result4 = mysqli_query($connect, $sql4);
//                    if ($result4) {//팔로받은사람 = 로그인한사람, 팔로우한사람 = 좋아요한사람 일 경우, "팔로우 반환"
//                        $follow = "팔로우";
//                    } else {
//                        $follow = "팔로잉";
//                    }
//                }

                $sql2 = "SELECT n.id, n.group, n.category, n.noti_desc, n.sender_no, n.receiver_no, n.sb_id, n.regdate, m.name, m.picture
FROM notification_list AS n, member_info AS m
WHERE n.sender_no = m.no
and n.receiver_no = '$no'
and n.category = '팔로우'
and n.id = '$id'";

                $result2 = mysqli_query($connect, $sql2);
                $count2 = mysqli_num_rows($result2);
                if ($count2 > 0) {

                    $row2 = mysqli_fetch_assoc($result2);
                    $response = array();
                    $response['id'] = $row2['id'];
                    $response['group'] = $row2['group'];
                    $response['category'] = $row2['category'];
                    $response['noti_desc'] = $row2['noti_desc'];
                    $response['sender_no'] = $row2['sender_no'];
                    $response['receiver_no'] = $row2['receiver_no'];
                    $response['sb_id'] = $row2['sb_id'];
                    $response['regdate'] = $row2['regdate'];
                    $response['user_name'] = $row2['name'];
                    $response['user_image'] = $row2['picture'];
                    $response['follow'] = $follow;
                    $response['image'] = "null";
                    array_push($notification_array, $response);
                }
            }
        }
        echo json_encode($notification_array);
    } else {
        echo "데이터가 없습니다.";
    }
} else { //값이 없는 경우
    echo "데이터가 없습니다.";

}