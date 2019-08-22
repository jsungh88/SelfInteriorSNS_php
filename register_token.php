<?php
/**
 * Created by PhpStorm.
 * User: Joanne
 * Date: 2018-07-05
 * Time: 오후 3:34
 */

header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

$token = $_POST["Token"] or $_GET["Token"];
$_SESSION["dtkn"] = $token;
$query = "INSERT INTO Tokens(Token) Values ('$token') ON DUPLICATE KEY UPDATE Token = '$token' ";
sql_query($query);

?>

