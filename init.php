<?php
/**
 * Created by PhpStorm.
 * User: Joanne
 * Date: 2018-06-05
 * Time: 오후 2:25
 */
header('content-type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set("display_errors", 1);
$host = 'host';
$username = 'user_id';
$password = 'password';
$database = 'databasename';

//데이터베이스 접속 문자열 (db위치, 유저이름, 비밀번호)
//$connect = new mysqli($host,$username,$password,$database);
$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

?>