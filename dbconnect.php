<?php
//データベース接続文字列
$dsn = 'mysql:host=db;dbname=mini_bbs;charset=utf8mb4';
$user = 'root';
$password = 'secret';

//-------データベース接続-------
try { //例外処理
    $db = new PDO($dsn, $user, $password); //情報をdbに格納
} catch (PDOException $e) { //送られてきた例外処理をeに格納する
    echo 'DB接続失敗です' . $e->getMessage();
}