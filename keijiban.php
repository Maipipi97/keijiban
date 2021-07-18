<?php
    //データベースに接続
    $dsn = 'mysql:dbname=DBNAME;host=localhost';
    $user = 'USERNAME';
    $password = 'PASSWORD';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //データベースにテーブル作成(keijiban)//成功
    $sql = "CREATE TABLE IF NOT EXISTS keijiban"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "password TEXT,"
    . "time DATETIME"
    .");";
    //SQLを実行
    $stmt = $pdo->query($sql);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body bgcolor="#3B5B85" text= "#EEBDD4">
    <span style="font-size: 40px">簡易掲示板</span><br>
    <span style="font-size: 15px">※数字を入力するときは半角で<br></span>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $edit_id = $_POST["edit_num"];//編集する投稿番号
    $edit_pass = $_POST["edit_pass"];
    
    if (!empty($edit_id)){//編集処理
        if (is_numeric($edit_id)){//編集番号が半角数字かどうか
        
            $sql = 'SELECT * FROM keijiban WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    $check_pass = $row["password"];
                }
                
            if (!empty($edit_pass)){
                if ($edit_pass == $check_pass){
                    $sql = "SELECT * FROM keijiban WHERE id =:id";
                    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                    $stmt->bindParam(':id', $edit_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                    $stmt->execute();                             // ←SQLを実行する。
                    $results = $stmt->fetchAll(); 
                    foreach ($results as $row){
                        //$rowの中にはテーブルのカラム名が入る
                        $name_save = $row["name"];//編集対象の名前を保存
                        $comment_save = $row["comment"];//編集対象のコメントを保存
                        $pass_save = $row["password"];
                    }
                }else{
                    "パスワードが違います<br>";
                }
            }else{
                echo "パスワードを入力してください<br>";
            }
        }else{
            echo "番号は半角数値で入力してください<br>";
        }
    }
}
?>
    <form action="" method="post"><!action="URL"で送信先指定>
        <input type="hidden" name="hidden_edit_num" value="<?php if(!empty($edit_id)){echo $edit_id;}?>">
        <input type="text" name="name" value = "<?php if(!empty($name_save)){echo $name_save;}?>" placeholder="<?php if(empty($name_save)){echo "名前を入力";}?>">
        <input type="text" name="comment" value="<?php if(!empty($comment_save)){echo $comment_save;}?>"placeholder="<?php if(empty($comment_save)){echo "コメントを入力";}?>">
        <input type="text" name="pass" value = "<?php if(!empty($pass_save)){echo $pass_save;}?>" placeholder="<?php if(empty($pass_save)){echo "パスワードを入力";}?>">
        <input type="submit" name="submit" value="新規投稿"><br>
        <input type="num" name="delete_num" placeholder="削除番号を入力">
        <input type="text" name="delete_pass" placeholder="パスワードを入力">
        <input type="submit" name="delete" value="削除"><br>
        <input type="num" name="edit_num" placeholder="編集番号を入力">
        <input type="text" name="edit_pass" placeholder="パスワードを入力">
        <input type="submit" name="edit" value="編集">
    </form>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
    $delete_id = $_POST["delete_num"];
    $delete_pass = $_POST["delete_pass"];
    $hidden_edit_num = $_POST["hidden_edit_num"];
    $date = date("Y-m-d H:i:s");

    if ($name != "" and $comment != ""){//新規投稿or投稿編集
        if ($hidden_edit_num != ""){//投稿編集
            $sql = 'UPDATE keijiban SET name=:name,comment=:comment, time=:time, password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':time', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $hidden_edit_num, PDO::PARAM_INT);
            $stmt->execute();
            echo "編集完了<br>";
            
        }else{//新規投稿ならば保存してブラウザに表示
            //テーブル内にレコード（データ）を登録
            $sql = $pdo -> prepare("INSERT INTO keijiban (name, comment, time, password) VALUES (:name, :comment, :time, :password)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(":password", $pass, PDO::PARAM_STR);
            $sql ->bindParam(':time', $date, PDO::PARAM_STR);
            $sql -> execute();//DBに問い合わせのクエリ
            
            $sql = 'SELECT * FROM keijiban';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                //パスワードは表示しない
                echo $row['time'].'<br>';
                echo "<hr>";//横線を引く
            }
        }
    }elseif ($delete_id != ""){//削除処理
        if (is_numeric($delete_id)){
            
            $sql = 'SELECT * FROM keijiban WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    $check_pass = $row["password"];
                }
            
            if (!empty($delete_pass)){
                if ($delete_pass == $check_pass){
                    $sql = 'delete from keijiban where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
                    $stmt->execute();
                    echo "削除しました<br>";
                }else{
                    echo "パスワードが違います<br>";
                }
            }else{
                echo "パスワードを入力してください<br>";
            }
        }else{
            echo "番号は半角数値で入力してください<br>";
        }
    }
}
?>