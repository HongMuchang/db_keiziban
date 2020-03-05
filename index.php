<?php
session_start();
//----データベースに接続----
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) { //一時間何もしないでいると
    //ログインしている時
    $_SESSION['time'] = time(); //現在時刻に上書き
    $members = $db->prepare('SELECT * FROM members WHERE id=?'); //メンバーの情報を引き出す
    $members->execute(array($_SESSION['id'])); //会員情報を引き出す
    $member = $members->fetch(); //複数形変数から単数形変数にfetchをして所得しデータを保存
} else {
    //ログインしていないとき
    header('Location:login.php');
    exit();
}


if (!empty($_POST)) { //投稿するボタンが押されたら
    if ($_POST['message'] !== "") {
        //postテーブルにメンバーIDとメッセージをいれる

        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=0, created=NOW()');
        $message->execute(array(
            @$member['id'], //データベースから撮ってきたID
            $_POST['message']
        ));
        header('Location:index.php');
        exit();
    }
}

/*ーーーーーページングーーーーー*/
@$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);
//最終ページの取得
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxpage = ceil($cnt['cnt'] / 5); //データベースから取得したcntを5で割って少数を切り上げる
$page = min($page, $maxpage);

$start = ($page - 1) * 5; //ページングの計算

$posts = $db->prepare('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE m.id = p.member_id ORDER BY p.created DESC LIMIT ?,5');

$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


//ーーーーー返信処理ーーーーー
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name,m.picture,p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
    @$response->execute(array($_REQUEST['rec']));
    $table = $response->fetch();
    @$message = '@' . $table['name'] . '' . $table['message'];
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <form action="" method="post">
                <dl>
                    <dt><?php echo @htmlspecialchars($member['name'], ENT_QUOTES); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message" cols="50"
                            rows="5"><?php echo @htmlspecialchars($message, ENT_QUOTES); ?></textarea>
                        <input type="hidden" name="reply_post_id"
                            value="<?php echo @htmlspecialchars($_REQUEST['res'], ENT_QUOTES); ?>" />
                    </dd>
                </dl>
                <div>
                    <p>
                        <input type="submit" value="投稿する" />
                    </p>
                </div>
            </form>
            <?php foreach ($posts as $post) : ?>
            <div class="msg">
                <!--画像表示と投稿者の名前表示-->
                <img src="member_picture/<?php echo @htmlspecialchars($post['picture'], ENT_QUOTES); ?>" width="48"
                    height="48" alt="<?php echo @htmlspecialchars($post['name'], ENT_QUOTES); ?>" />

                <!--メッセージ表示と投稿者の名前表示-->
                <p><?php echo @htmlspecialchars($post['message'], ENT_QUOTES); ?>
                    <span class="name">（<?php echo @htmlspecialchars($post['name'], ENT_QUOTES); ?>）</span>[<a
                        href="index.php?res=<?php echo @htmlspecialchars($post['id'], ENT_QUOTES); ?>">Re</a>]</p>

                <!--作成時間表示-->
                <p class="day"><a href="view.php?id=<?php echo @htmlspecialchars($post['id']); ?>">
                        <?php echo @htmlspecialchars($post['created'], ENT_QUOTES); ?></a>

                    <!--まだ投稿していない人の返信のリンクを非表示-->
                    <?php if ($post['reply_message_id'] > 0) : ?>
                    <a href="view.php?id=<?php echo @htmlspecialchars($post['reply_message_id']); ?>">
                        返信元のメッセージ</a>
                    <?php endif; ?>

                    <!--自分の投稿のみ削除のリンクを表示-->
                    <?php if ($_SESSION['id'] == $post['member_id']) : ?>
                    [<a href="delete.php?id=<?php echo @htmlspecialchars($post['id']); ?>" style="color: #F33;">削除</a>]
                    <?php endif; ?>
                </p>
            </div>
            <?php endforeach; ?>

            <ul class="paging">
                <?php if ($page > 1) : ?>
                <li><a href="index.php?page=<?php echo $page - 1; ?>">前のページへ</a></li>
                <?php else : ?>
                <li>前のページへ</li>
                <?php endif; ?>
                <?php if ($page < $maxpage) : ?>
                <li><a href="index.php?page=<?php echo $page + 1; ?>">次のページへ</a></li>
                <?php else : ?>
                <li>次のページ</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>

</html>