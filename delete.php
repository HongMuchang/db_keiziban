<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) { //ログインしているユーザーのメッセージを消そうとしているかの判断
    //自分のメッセージかの判断はデータベースからロードしないと分からないため
    //データベースから削除する候補を取得

    $id = $_REQUEST['id'];

    $message = $db->prepare('SELECT * FROM posts WHERE id=?');
    $message->execute(array($id)); //SQL分のid=?(URLパラメーターで渡された)が
    $message = $message->fetch(); //単数の変数にデータを取得

    if ($message['member_id'] == $_SESSION['id']) { //データベースの中のmember_idと＄SESSION[id]が一緒の時に削除が可能
        $del = $db->prepare('DELETE FROM posts WHERE id=?'); //削除文章
        $del->execute(array($id)); //実際に削除
    }
}
header('Location:index.php');
exit();