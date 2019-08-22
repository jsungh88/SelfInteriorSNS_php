<?php header('content-type: text/html; charset=utf-8');
/**
 * Created by PhpStorm.
 * User: Joanne
 * Date: 2018-07-05
 * Time: 오후 4:13
 */
require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');



public
function index()
{
    $sql = "select Tokens from userToken";

    $result = DB::select($sql);
    $tokens = array();

    if (sizeof($result) > 0) {
        foreach ($result as $Result) {
            $tokens[] = $Result->Token;
        }
    }

    $myMessage = "새글이 등록되었습니다.";
    $message = array("message" => $myMessage);

    $url = 'https://fcm.googleapis.com/fcm/send';
    $fields = array(
        'registration_ids' => $tokens,
        'data' => $message
    );

    $headers = array(
        'Authorization:key =' . 'API_KEY'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    echo $result;
}


?>