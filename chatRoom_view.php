<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

/**
 * < 참여한 채팅방 리스트 or 참여하지않은 채팅방 리스트 >
 *
 * 1. 참여한 채팅방 리스트
 *  0) 사용자no = no, inorout= in
 *  1) 채팅방 + 채팅방참여자 테이블을 조인하여, 로그인유저 id를 조건으로 걸고, 로그인유저 id가 있는방을 참여하고 있는 채팅방으로 한다.
 *  2) 클라이언트로 보낸다.
 *
 * 2. 참여하지 않은 채팅방 리스트
 *  0) 사용자no = no, inorout= out
 *  1) 채팅방 + 채팅방참여자 테이블을 조인하여, 로그인유저 id를 조건으로 걸고, 로그인유저 id가 없는방을 참여하고있지않은 채팅방으로 한다.
 *  2) 클라이언트로 보낸다.
 *
 */

if (isset($_POST['no'])) { //사용자no를 받아온다.
    $no = $_POST['no'];
}
if (isset($_POST['inorout'])) { //in or out(참여중O or 참여중X)
    $inorout = $_POST['inorout'];
}
$ok = false;

if (isset($no) and isset($inorout)) {
// $inorout = in이면 참여중인 채팅리스트, 아니면 참여중이지 않은 채팅리스트
    $str = strcmp("in", $inorout);
    if ($str) {//다르다 = 참여중이지 않은 채팅리스트
        $sql1 = "SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id = $no"; //로그인유저의 참여중인 채팅방이 있으면?
        $result1 = mysqli_query($connect, $sql1);
        $count = mysqli_num_rows($result1);
        if ($count == 0) {//없으면?
//            $sql3 = "SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id";
            $sql3 = "SELECT * FROM chatroom";
            $result3 = mysqli_query($connect, $sql3);
            $chatroom_array = array();
            while ($row = mysqli_fetch_assoc($result3)) {
                $response = array();
                $response['room_id'] = $row['id'];
                $response['room_name'] = $row['roomname'];
//        $response['chatroom_image'] = $row['chatroom_image'];
//        $response['message'] = $row['message'];
//        $response['time'] = $row['time'];
                array_push($chatroom_array, $response);
            }
        } else {//있으면?
            /**
             * 유저no가 있던 방리스트를 가져오고 방id를 구한다.
             * 유저가 없는 방목록을 가져온다.
             * 만약
             */
//            $sql4 = "SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id IS NULL";
//            $sql4 ="SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id != '$no'";
            $sql4 = "SELECT r.id, r.roomname,p.FK_user_id FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id='$no'"; //참여중인 방
            $result4 = mysqli_query($connect, $sql4);
            $count4 = mysqli_num_rows($result4);
            $chatroom_array = array();
//            if ($count4 == 1) { //참여중인방이 1개일 경우에는, 그 방을 제외한
//                $sql5 = "SELECT r.id, r.roomname,p.FK_user_id FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id!='$no' GROUP BY r.roomname"; //참여중이지않은 방
//                $result5 = mysqli_query($connect, $sql5);
//                while ($row5 = mysqli_fetch_assoc($result5)) {
//                    $response['room_id'] = $row5['id'];
//                    $response['room_name'] = $row5['roomname'];
////                $response['message'] = $row5['message'];
////                $response['time'] = $row5['time'];
//                    array_push($chatroom_array, $response);
//                }
//            } else {


            while ($row4 = mysqli_fetch_assoc($result4)) {
                $room_ido = $row4['id']; //참여중인 방id
                $sql5 = "SELECT r.id, r.roomname,p.FK_user_id FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id!='$no' GROUP BY r.roomname"; //참여중이지않은 방
                $result5 = mysqli_query($connect, $sql5);
                while ($row2 = mysqli_fetch_assoc($result5)) {
                    $room_idx = $row2['id']; //참여중이지않은 방id
                    $str = strcmp($room_ido, $room_idx); //비교
                    if (!$str) {//같으면 해당방번호 제외한 걸 어레이에 푸시..
                        $sql6 = "SELECT r.id, r.roomname,p.FK_user_id FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id!='$no' and r.id!='$room_idx' GROUP BY r.roomname";
                        $result6 = mysqli_query($connect, $sql6);
                        while ($row6 = mysqli_fetch_assoc($result6)) {
                            $response['room_id'] = $row6['id'];
                            $response['room_name'] = $row6['roomname'];
//                $response['message'] = $row2['message'];
//                $response['time'] = $row2['time'];
                            array_push($chatroom_array, $response);
                        }
                    } else {//같지가 않은데, 참여중인방 개수가 하나면, 그것만 제외한 방리스트를 보여주면됨 !

                    }
                }
                if ($no == 3) {
                    if ($str) {
                        if ($count4 == 1) {
                            $sql7 = "SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id != '$no' and r.id!='$room_ido' GROUP BY r.roomname";
                            $result7 = mysqli_query($connect, $sql7);
                            while ($row7 = mysqli_fetch_assoc($result7)) {
                                $response['room_id'] = $row7['id'];
                                $response['room_name'] = $row7['roomname'];
//                $response['message'] = $row2['message'];
//                $response['time'] = $row2['time'];
                                array_push($chatroom_array, $response);
                            }
                        }
                    }
                }
            }

//            } //여기임

        }
        echo json_encode($chatroom_array);
    } else {//같다 = 참여중인 채팅 리스트
        $sql2 = "SELECT r.id, r.roomname FROM chatroom r LEFT JOIN chat_participants p ON r.id = p.FK_room_id WHERE p.FK_user_id='$no'";
        $result2 = mysqli_query($connect, $sql2);
        $chatroom_array = array();
        while ($row = mysqli_fetch_assoc($result2)) {
            $response = array();
            $response['room_id'] = $row['id'];
            $response['room_name'] = $row['roomname'];
//        $response['chatroom_image'] = $row['chatroom_image'];
//        $response['message'] = $row['message'];
//        $response['time'] = $row['time'];
            array_push($chatroom_array, $response);
        }
        echo json_encode($chatroom_array);
    }
} else {
    echo "데이터가 없습니다.";
}

$connect->close();

?>