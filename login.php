<?php

//-------データベース接続-------
session_start();
require('dbconnect.php');

if (@$_COOKIE['email'] !== "") {
  @$email = $_COOKIE['email'];
  // @$_POST['password'] = $_COOKIE['password'];
  // $_POST['save'] = 'on';
}

/*-------ログイン機能-------*/
if (!empty($_POST)) { //ボタンが押されたかの判断
  @$email = $_POST['email']; //emailの上書き

  if (@$_POST['email'] !== "" && $_POST['password'] !== "") { //メールとパスワード入力されていたら

    //データベースに問合せ準備
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');

    //$_POSTと見比べることで確認出来る
    $login->execute(array(
      @$_POST['email'],
      sha1($_POST['password']) //SHA1で暗号化されているので暗号化されている同士で参照する
    ));

    //データが帰ってきていればログイン成功、帰ってこなかったらログイン失敗
    $member = $login->fetch();

    //ture,falseでログインに成功していたら処理に入る
    if ($member) {
      $_SESSION['id'] = $member['id']; //$_SESSION['id']にIDが格納されていて
      $_SESSION['time'] = time(); //ログインしたときの時刻が書かれている

      if ($_POST['save'] == 'on') { //$_SESSIONがセットされているか
        setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14); //移動する前にメールアドレスをcookieにを保存する
        // setcookie('password', $_POST['password'], time() + 60 * 60 * 24 * 14);
      }

      header('Location:index.php');
      exit();
    } else {
      $error['login'] = 'failed'; //エラーを記憶
    }
  } else {
    $error['login'] = 'blank'; //エラーならblankを格納
  }
}

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <title>ログインする</title>
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ログインする</h1>
        </div>
        <div id="content">
            <div id="lead">
                <p>メールアドレスとパスワードを記入してログインしてください。</p>
                <p>入会手続きがまだの方はこちらからどうぞ。</p>
                <p>&raquo;<a href="join/">入会手続きをする</a></p>
            </div>
            <form action="" method="post">
                <dl>
                    <dt>メールアドレス</dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255"
                            value="<?php echo @htmlspecialchars($email, ENT_QUOTES); ?>" />
                        <!--mailエラーの識別-->
                        <?php if (@$error['login'] === 'blank') : //メール=blankの時エラー表示 :
            ?>
                        <p class="error">メールアドレスとパスワードをご記入ください</p>
                        <?php endif; ?>
                        <?php if (@$error['login'] === 'failed') : //failedの時エラー表示 :
            ?>
                        <p class="error">ログインに失敗しました</p>
                        <?php endif; ?>
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        <input type="password" name="password" size="35" maxlength="255"
                            value="<?php echo @htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
                    </dd>
                    <dt>ログイン情報の記録</dt>
                    <dd>
                        <!--save-->
                        <input id="save" type="checkbox" name="save" value="on">
                        <label for="save">次回からは自動的にログインする</label>
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="ログインする" />
                </div>
            </form>
        </div>
        <div id="foot">
            <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
        </div>
    </div>
</body>

</html>