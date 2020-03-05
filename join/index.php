<?php
require('../dbconnect.php'); //相対パス指定

session_start();

//配列が空＝呼び出された直後、
if (!empty($_POST)) { //空じゃなかったら処理開始
    /*入力されていないときのエラー*/
    if ($_POST['name'] === "") { //何も入力されていない条件
        $error['name'] = 'blank';
    }
    if ($_POST['email'] === "") { //何も入力されていない条件
        $error['email'] = 'blank';
    }
    if ($_POST['password'] === "") { //何も入力されていない条件
        $error['password'] = 'blank';
    }
    /*--------------------------*/
    if (strlen($_POST['password']) < 4) { //パスワードが4文字以下の時
        $error['password'] = 'length';
    }

    //画像がアップロードされてい@@@@@
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) { //正しくない場合
        $ext = substr($fileName, -3); //切り取った拡張子３文字がjpg or gifの場合は
        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') //画像がjpgとgifとpng出なければ
            $error['image'] = 'type'; //エラーをtypeにする
    }
    //重複チェックエラー@@@@@
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt' > 0]) {
            $error['email'] = 'duplicate';
        }
    }

    /*---もしエラーが発生してない時の処理@@@@---*/
    if (empty($error)) { //emptyは配列がからかどうか
        $image = date('YmdHis') . $_FILES['image']['name']; //アップロードする時時間、ファイル名を記載
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image); //アップロードされたファイルを新しい位置に移動する
        $_SESSION['join'] = $_POST; //エラーじゃないときに$POSTをSESSION['join']に格納,二次元配列になる
        $_SESSION['join']['image'] = $image; // $_SESSION['join']という配列の中に['image']というkeyを作りファイル名を保管
        header('Location:check.php');
        exit();
    }
}
//書き直しURLが(rewrite)@@@@
if (@$_REQUEST['action'] == 'rewrite') {
    $POST = $_SESSION['join'];
    @$error['rewrite'] == true;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta http-equiv="content-type" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>会員登録</title>

    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>
        </div>

        <div id="content">
            <p>次のフォームに必要事項をご記入ください。</p>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt>ニックネーム(英語)<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="name" size="35" maxlength="255"
                            value="<?php print @(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>" />
                        <!--nameエラーの識別-->
                        <?php if (@$error['name'] == 'blank') : //name=blankの時エラー表示
                        ?>
                        <p class="error">ニックネームを入力してください</p>
                        <?php endif; ?>

                    </dd>
                    <dt>メールアドレス<span class="required">必須</span></dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255"
                            value="<?php print @(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>" />
                        <!--emailエラーの識別-->
                        <?php if (@$error['email'] == 'blank') : //メール=blankの時エラー表示
                        ?>
                        <p class="error">メールを入力してください</p>
                        <?php endif; ?>
                        <!--メールのエラー-->
                        <?php if (@$error['email'] == 'duplicate') : ?>
                        <p class="error">*指定されたメールアドレスはすでに登録されています。*</p>
                        <?php endif; ?>

                    </dd>
                    <dt>パスワード<span class="required">必須</span></dt>
                    <dd>
                        <input type="password" name="password" size="10" maxlength="20"
                            value="<?php print @(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
                        <!--passwordエラーの識別-->
                        <?php if (@$error['password'] == 'length') : //パスワード4文字以下の時lengthを受け取りエラー表示
                        ?>
                        <p class="error">4文字以上で入力してください</p>
                        <?php endif; ?>
                        <?php if (@$error['password'] == 'blank') : //パスワード=blankの時エラー表示
                        ?>
                        <p class="error">パスワードを入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <input type="file" name="image" size="35"
                            value="<?php print @(htmlspecialchars($_POST['image'], ENT_QUOTES)); ?>" />
                        <!--fileエラーの識別-->
                        <?php if (@$error['image'] == 'type') : //image=typeの時エラー表示
                        ?>
                        <p class="error">写真などは[.gif],[.jpg],[.png]</p>
                        <?php endif; ?>
                        <?php if (!empty($error)) : //image=typeの時エラー表示
                        ?>
                        <p class="error">恐れ入りますが、画像を改めてください</p>
                        <?php endif; ?>

                    </dd>
                </dl>
                <div><input type="submit" value="入力内容を確認する" /></div>
            </form>
        </div>
        <?php

        ?>
</body>

</html>