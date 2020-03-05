<?php
session_start();

//-------データベース接続-------
require('../dbconnect.php'); //相対パス指定

/*--indexから送られたSESSION['join']がない時--*/
if (!isset($_SESSION['join'])) {
    header('Location:index.php'); //indexに戻す
    exit();
}
/*------------------------------------------*/


/*----登録画面を押されたらデータベースに保存する@@@@----*/
if (!empty($_POST)) {
    //登録処理をする
    $statement = $db->prepare('INSERT INTO members SET name=?,email=?,password=?,picture=?,created=NOW()');
    echo $ret = $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        sha1($_SESSION['join']['password']), //パスワードは暗号化する
        $_SESSION['join']['image'],
    ));
    unset($_SESSION['join']); //$SESSIONの中身を空にする、上で保存しているので空にする
    header('Location:thanks.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta http-equiv="content-type" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>会員登録(結果画面)</title>

    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>
        </div>

        <div id="content">
            <p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submit" />
                <dl>
                    <dt>ニックネーム</dt>
                    <dd>
                        <!--確認画面name表示-->
                        <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES); ?>
                    </dd>
                    <dt>メールアドレス</dt>
                    <dd>
                        <!--確認画面mail表示-->
                        <?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES); ?>
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        <!--確認画面password非表示-->
                        【表示されません】
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <!--確認画面image表示-->
                        <?php if ($_SESSION['join']['image'] !== '') : //空でなければimg表示　
                        ?>
                        <img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES); ?>"
                            width="100" height="100" alt="">
                        <?php endif; ?>
                    </dd>
                </dl>
                <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" />
                </div>
            </form>
        </div>

    </div>
</body>

</html>