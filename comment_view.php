<?php header('content-type: text/html; charset=utf-8');

require "init.php";

$connect = mysqli_connect($host, $username,
    $password, $database) or die("SQL서버에 연결할 수 없습니다.");
mysqli_set_charset($connect, 'utf8');

if (isset($_POST['sb_id'])) {
    $sb_id = $_POST['sb_id'];
}

if (isset($_POST['writer_no'])) {
    $writer_no = $_POST['writer_no'];
}

/**
 * <댓글 보기>
 * 1. 글id(sb_id), 작성자no(writer_no)를 전달받는다.
 * 2. 댓글 테이블(sb_comment_list)에서 글id를 가지고 있는 데이터를 불러온다.
 * 3. 회원정보 테이블(member_inf)에서 작성자no를 가지고 있는 데이터를 불러온다.
 * 4. 댓글,회원정보 테이블을 조인하여 필요한 정보를 가져온다.
 * 3. array()에 담아 클라이언트로 저장한다.
 */

if(isset($sb_id) and isset($writer_no)) {//값이 있는 경우
    $sql1 = "SELECT c.id, c.sb_id, c.order, c.depth, c.comment, c.regdate, c.group, m.no, m.name, m.picture
    FROM sb_comment_list AS c
    JOIN member_info AS m
    ON c.writer_id = m.no
    WHERE c.sb_id = '$sb_id'
    ORDER BY `group` ASC, depth ASC, `order` ASC";
    $result1 = mysqli_query($connect,$sql1);
    $count1 = mysqli_num_rows($result1);

    if ($count1 > 0) {
        $comments_array = array();
        while($row = mysqli_fetch_assoc($result1)){
            $response = array();
            $response['id']= $row['id'];
            $response['sb_id']= $row['sb_id'];
            $response['order']= $row['order'];
            $response['depth']= $row['depth'];
            $response['comment']= $row['comment'];
            $response['regdate']= $row['regdate'];
            $response['group'] = $row['group'];
            $response['writer_no']= $row['no'];
            $response['writer_id']= $row['name'];
            $response['writer_image']= $row['picture'];

            array_push($comments_array, $response);
        }
        echo json_encode($comments_array);



    }else{
        echo "데이터가 없습니다.";
    }
}elseif(empty($sb_id)){//값이 없는 경우
    echo "데이터가 없습니다.";
}

$connect->close();

?>