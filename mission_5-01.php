<?php


// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$table = 'tbtest_5111';
$sql = "CREATE TABLE IF NOT EXISTS $table"
    . " ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "password TEXT,"
    . "regi_date TEXT"
    . ");";

$name = $_POST["name"];
$comment = $_POST["comment"];
$pass = $_POST["password"];
$date = date("Y年m月d日H時i分s秒");


if (!empty($name) && !empty($comment) && !empty($pass) && empty($_POST["keep_editNo"])) {
    $stmt = $pdo->query($sql);
    $sql = $pdo->prepare("INSERT INTO $table (name, comment, password,regi_date) VALUES (:name, :comment , :password, :regi_date)");
    $sql->bindParam(':name', $name, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':password', $pass, PDO::PARAM_STR);
    $sql->bindParam(':regi_date', $date, PDO::PARAM_STR);
    $sql->execute();
}

//削除
if (isset($_POST["btn_delete"])) {
    $deleteNo = $_POST['deleteNo'];
    $delete_pass = $_POST['pass'];

    $sql = "SELECT id, password FROM $table WHERE id=$deleteNo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    foreach ($stmt as $row) {
        $num = $row['id'];
        $pass = $row['password'];
    }

    if ($pass == $delete_pass) {
        $sql = "DELETE FROM $table WHERE id=$num";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } else {
        echo "パスワードまたは削除番号が間違っています";
        exit();
    }
}

//編集　→　投稿
if (isset($_POST['btn_edit'])) {

    if ((empty($_POST['editNo'])) || (empty($_POST['password']))) {
        echo '編集番号とパスワードの両方を入力してください';
        exit();
    }

    $editNo = $_POST['editNo'];
    $edit_pass = $_POST['password'];

    $sql = "SELECT id,password FROM $table WHERE id=$editNo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    foreach ($stmt as $row) {
        $num = $row['id'];
        $pass = $row['password'];
    }

    if ($pass == $edit_pass) {
        $sql = "SELECT * FROM $table WHERE id=$num";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        foreach ($stmt as $row) {
            $edit_no = $row['id'];
            $edit_name = $row['name'];
            $edit_comment = $row['comment'];
            $edit_pass = $row['password'];
        }
    } else {
        echo "パスワードまたは編集番号が間違っています";
        exit();
    }
}
//編集
if (isset($_POST['execute_edit']) && isset($_POST["keep_editNo"])) {
    $editNo = $_POST['keep_editNo'];
    $sql = "UPDATE $table SET name=:name,comment=:comment,password=:password,regi_date=:regi_date WHERE id= $editNo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name',  $_POST['name'], PDO::PARAM_STR);
    $stmt->bindParam(':comment',  $_POST['comment'], PDO::PARAM_STR);
    $stmt->bindParam(':password',  $_POST['password'], PDO::PARAM_STR);
    $stmt->bindParam(':regi_date', $date, PDO::PARAM_STR);
    $stmt->execute();
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mission_5-01</title>
</head>

<body>
    <form action="" method="POST">
        <input type="text" name="name" placeholder="名前" value="<?php if (isset($_POST['btn_edit'])) {
                                                                    echo $edit_name;
                                                                } ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if (isset($_POST['btn_edit'])) {
                                                                        echo $edit_comment;
                                                                    } ?>">
        <input type="text" name="keep_editNo" value="<?php if (isset($_POST['btn_edit'])) {
                                                            echo $edit_no;
                                                        } ?>">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="btn_submit" value="投稿">
        <input type="submit" name="execute_edit" value="編集">
        <!--削除フォーム-->
        <input type="削除番号" name="deleteNo" placeholder="削除番号">
        <input type="submit" name="btn_delete" value="投稿を削除">

        <!--編集フォーム-->
        <input type="text" name="editNo" placeholder="編集対象番号">
        <input type="submit" name="btn_edit" value="投稿を編集">



    </form>

    <?php

    $sql = "SELECT * FROM $table";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {

        echo $row['id'] . ',';
        echo $row['name'] . ',';
        echo $row['comment'] . ",";
        echo $row['password'] . ",";
        echo $row['regi_date'] . "<br>";
    }

    ?>
</body>

</html>