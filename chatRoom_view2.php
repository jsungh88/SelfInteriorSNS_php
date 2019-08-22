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

        $sql = "select c.id, c.roomname, c.leader,p.FK_user_id  from chatroom c left join  chat_participants p on c.id=p.FK_room_id where FK_user_id='$no'";
        $result = mysqli_query($connect, $sql);
        $roomid_arr = array();
        //유저가  참여안한 방
        while($row  = mysqli_fetch_assoc($result)){

            $room_id = $row['id'];
//            echo "room_id".$room_id;
            array_push($roomid_arr,$room_id);
        }

        //조건문 만들기
        $size = count($roomid_arr);
        for($i=0;  $i<$size; $i++){
            $s = $roomid_arr[$i];

            if($i==0){
                $condition = "c.id!="."$s";
            }else{
                $condition = "$condition"." and c.id!="."$s";
            }
//            echo "condition:".$condition;
        }

        $sql1 = "select * from chatroom c left join member_info m on c.leader=m.no where "."$condition";
//        echo "sql:".$sql1;
        $result1 = mysqli_query($connect, $sql1);
        $count1 = mysqli_num_rows($result1);
        $chatroom_array = array();
        if ($count1 > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $response = array();
                $roomid  = $row['id'];
                $response['room_id'] = $row['id'];
//        $response['chatroom_image'] = $row['chatroom_image'];
                $response['room_name'] = $row['roomname'];
                $response['leaderId'] = $row['leader'];
                $response['leaderName'] = $row['name'];
                $response['leaderImage'] =  $row['picture'];

                $sql3 = "select * from chat_participants where FK_room_id='$roomid'";
                $result3 = mysqli_query($connect,$sql3);
                $count3 = mysqli_num_rows($result3);
                $response['count'] = $count3;

                $response['kindof'] = "out";
                $response['first']="true";

                array_push($chatroom_array, $response);
            }
        }
        echo json_encode($chatroom_array);
    } else {//같다 = 참여중인 채팅 리스트
        $sql2 = "select r.id, r.roomname, r.leader, m.name, m.picture FROM member_info m, chatroom r JOIN chat_participants p ON r.id=p.FK_room_id WHERE p.FK_user_id ='$no' and r.leader=m.no";
        $result2 = mysqli_query($connect, $sql2);
        $count2 = mysqli_num_rows($result2);
        $chatroom_array = array();
        while ($row = mysqli_fetch_assoc($result2)) {
            $response = array();
            $room = $row['id'];
            $response['room_id'] = $row['id']; //방id
            $response['room_name'] = $row['roomname']; //방이름
            $response['first'] = "false"; //첫입장인지! 아닌지! 참여중이었으므로 false
//        $response['chatroom_image'] = $row['chatroom_image'];

//            $leaderid  = $row['leader'];
//            //방장정보
//            $sql5= "select * from member_info where no='$leaderid'";
//            $result5 = mysqli_query($connect, $sql5);
//            $row5 = mysqli_fetch_assoc($result5);
            $response['leaderId'] = $row['leader'];
            $response['leaderName'] = $row['name'];
            $response['leaderImage'] =  $row['picture'];

            //방참여자수
            $sql4 = "select * from chat_participants where FK_room_id='$room'";
            $result4 = mysqli_query($connect,$sql4);
            $count4 = mysqli_num_rows($result4);

            $response['count'] = $count4;

            $sql7 = "SELECT * FROM chat_message WHERE FK_room_id='$room' AND who NOT IN('notice') order by time DESC limit 1";
            $result7 = mysqli_query($connect, $sql7);
            $row7 = mysqli_fetch_assoc($result7);
            $message = $row7['message']; //최근메세지
            $time = $row7['time']; //메세지보낸시간
            $who = $row7['who'];
            $response['message'] = $message;

            //만약 $who가 receiver_image이거나, sender_image일 경우, "사진"으로 변경하기 + who가 'notice' 인 것은 애초 제외
            $str1 = strcmp("$who", "receiver_image");
            $str2 = strcmp("$who", "sender_image");
            if (!$str1 or !$str2) {
                $response['message'] = "사진"; //최근메세지가 사진일  경우
            }
            $db_date = $time;
            $date = date("A h:m", strtotime($db_date));

            $response['time'] = $date;  //변형된 메세지보낸시간
            $response['kindof'] = "in";
            array_push($chatroom_array, $response);
        }
        echo json_encode($chatroom_array);
    }
} else {
    echo "데이터가 없습니다.";
}
$connect->close();
?>