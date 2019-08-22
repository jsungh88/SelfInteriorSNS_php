<?php header('content-type: text/html; charset=utf-8');

define("GOOGLE_SERVER_KEY", "FCM_API_KEY");
function send_fcm($message, $id)
{
    $url = 'https://fcm.googleapis.com/fcm/send';

    $headers = array (
        'Authorization: key=' . GOOGLE_SERVER_KEY,
        'Content-Type: application/json'
    );

    $fields = array (
        'data' => array ("message" => $message),
        'notification' => array ("body" => $message)
    );

    if(is_array($id)) {
        $fields['registration_ids'] = $id;
    } else {
        $fields['to'] = $id;
    }

    $fields['priority'] = "high";

    $fields = json_encode ($fields);

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

    $result = curl_exec ( $ch );
    if ($result === FALSE) {
//die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close ( $ch );
    return $result;
}


?>


<!--//$sql = "select TOKEN from userToken where LENGTH(TOKEN)>63";-->
<!--//$result = mysqli_query($connect,$sql);-->
<!--//$tokens = array();-->
<!--//if(mysqli_num_rows($result)>0){-->
<!--//    while($row = mysqli_fetch_assoc($result)){-->
<!--//        $tokens[] = $row['TOKEN'];-->
<!--//    }-->
<!--//}else{-->
<!--//    echo 'There are no Transfer Datas';-->
<!--//    exit;-->
<!--//}-->
<!--//mysqli_close($connect);-->
<!--//$title = isset($_POST['title'])? $_POST['title']:"PUSH TEST";-->
<!--//$message = isset($_POST['message'])? $_POST['message']:"새 글이 등록되었습니다";-->
<!--//-->
<!--//$arr = array();-->
<!--//$arr['title'] = $title;-->
<!--//$arr['message'] = $message;-->
<!---->
<!--$message_status = send_fcm($token, $noti_desc);-->
<!--//푸시 전송 결과 반환-->
<!--$obj = json_decode($message_status);-->
<!---->
<!--//푸시 전송시 성공 수량 반환-->
<!--$cnt = $obj->{"success"};-->
<!--echo $cnt;-->
<!---->
<!--function send_fcm($id,$message)-->
<!--{-->
<!--$url = 'https://fcm.googleapis.com/fcm/send';-->
<!--$apiKey = "AAAAdAVYuLQ:APA91bHE9_rEv8yg8k06HfXMmZ24Ho9JbVNclJDbrjcIGJZSwwATqYiUViZxa0WeOD4i-yb4QycldE7QXGugEntqNSXTtalp2NHUuu-NMqgvm_GHa-JL-BFCy_olmfj4218ItlJ0wMJ8JCAcLRMDYP_Ns7rDfyiaVg";-->
<!---->
<!--$fields = $message;-->
<!--$headers = array('Authorization:key='.$apiKey,'Content-Type: application/json');-->
<!---->
<!--$ch = curl_init ();-->
<!--curl_setopt ( $ch, CURLOPT_URL, $url );-->
<!--curl_setopt ( $ch, CURLOPT_POST, true );-->
<!--curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );-->
<!--curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );-->
<!--curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0);-->
<!--curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );-->
<!--curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );-->
<!--$result = curl_exec ( $ch );-->
<!--if ($result === FALSE) {-->
<!--die('FCM Send Error: ' . curl_error($ch));-->
<!--}-->
<!--curl_close ( $ch );-->
<!--return $result;-->
<!--}-->
