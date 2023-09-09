<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        // DB接続設定
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザ名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        // 編集機能
        if( !empty( $_POST["editNum"] ) || !empty( $_POST["eN"] )  ){
            $passCheck = false;
            if( !empty( $_POST["editPass"] ) ) {
                if( !empty( $_POST["editNum"] ) ) {
                    $editNum = $_POST["editNum"];
                }else if ( !empty( $_POST["eN"] ) ) {
                    $editNum = $_POST["eN"];
                }
                $editPass = $_POST["editPass"];
                // パスワードが同じかチェック
                $sql = 'SELECT * FROM tb_5_1 WHERE id=:id';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $editNum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    if($editPass == $row['password']) $passCheck = true;
                }
                // パスワードが一致するときの処理
                if($passCheck) {
                    // 編集する行を取得
                    $sql = 'SELECT * FROM tb_5_1 WHERE id=:id';
                    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                    $stmt->bindParam(':id', $editNum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                    $stmt->execute();                             // ←SQLを実行する。
                    $results = $stmt->fetchAll();
                    foreach($results as $row){
                        // 編集する名前とコメントとパスワードを取得
                        $editName = $row['name'];
                        $editComment = $row['comment'];
                        $editPass = $row['password'];
                    }
                } else {
                    // パスワードが違うときの処理
                    if( !empty( $_POST["editNum"] ) ) {
                        echo "パスワードが違います。<br>";
                    }
                }
            } else {
                // パスワードが入力されていないときの処理
                if( !empty( $_POST["editNum"] ) ) {
                    echo "パスワードを入力して下さい。<br>";
                }
            }
        } else {
        }
        
        // 削除機能
        if( !empty( $_POST["delNum"] ) && empty( $_POST["editNum"] ) && empty( $_POST["name"] ) && empty( $_POST["txt"] ) ){
            if( !empty( $_POST["delPass"] ) ) {
                $delNum = $_POST["delNum"];
                $delPass = $_POST["delPass"];
                $passCheck = false;
                // パスワードが同じかチェック
                $sql = 'SELECT * FROM tb_5_1 WHERE id=:id';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $delNum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    if($delPass == $row['password']) $passCheck = true;
                }
                // パスワードが一致するときの処理
                if($passCheck) {
                    $sql = 'delete from tb_5_1 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $delNum, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // パスワードが違うときの処理
                    echo "パスワードが違います。<br>";
                }
            } else {
                // パスワードが入力されていないときの処理
                echo "パスワードを入力して下さい。<br>";
            }
        } else {
        }
    ?>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前"
        value="<?php
            if( !empty($editName) && empty( $_POST["eN"] ) && $passCheck) echo $editName;
        ?>"><br>
        <input type="text" name="txt" placeholder="コメント"
        value="<?php
            if( !empty($editComment) && empty( $_POST["eN"] ) && $passCheck) echo $editComment;
        ?>"><br>
        <input type="text" name="pass1" placeholder="パスワード"
        value="<?php
            if( !empty($editPass) && empty( $_POST["eN"] ) && $passCheck) echo $editPass;
        ?>"> <input type="submit" name="submit"><br>
        <p>
            <input type="number" name="delNum" placeholder="削除対象番号"><br>
            <input type="text" name="delPass" placeholder="パスワード"> <input type="submit" name="submit" value="削除">
        </p>
        <p>
            <input type="number" name="editNum" placeholder="編集対象番号"><br>
            <input type="text" name="editPass" placeholder="パスワード"> <input type="submit" name="submit" value="編集">
        </p>
        <p>
            <input type="hidden" name="eN" value="<?php
                if( !empty( $_POST["editNum"] ) && $passCheck) echo $editNum;
            ?>">
        </p>
    </form>
    
    <?php
        
        // 削除機能
        if( !empty( $_POST["delNum"] ) && empty( $_POST["editNum"] ) && empty( $_POST["name"] ) && empty( $_POST["txt"] ) ){
            if( !empty( $_POST["delPass"] ) ) {
                // 画面上に表示
                $sql = 'SELECT * FROM tb_5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    echo $row['id'].' ';
                    echo $row['name'].' ';
                    echo $row['comment'].' ';
                    echo $row['date'].'<br>';
                }
            }
        }
        
        // 書き込み機能
        if( !empty( $_POST["txt"] ) && !empty( $_POST["name"] ) && !empty( $_POST["pass1"] ) ){
            if( empty( $_POST["editNum"] ) && empty( $_POST["eN"] ) ) {
                // 新規投稿する
                $name = $_POST["name"];
                $txt = $_POST["txt"];
                $date = date("Y年m月d日 H時i分s秒");
                $pass = $_POST["pass1"];
                // SQL操作
                $sql = "INSERT INTO tb_5_1 (name, comment, date, password) VALUES (:name, :comment, :date, :pass)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $txt, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->execute();
            } else if ( !empty( $_POST["eN"] ) && empty( $_POST["editNum"] ) ) {
                // 編集したものを投稿する
                $editedName = $_POST["name"];
                $editedComment = $_POST["txt"];
                $editedDate = date("Y年m月d日 H時i分s秒");
                $editedPass = $_POST["pass1"];
                $editNum = $_POST["eN"];
                // SQL操作
                $sql = 'UPDATE tb_5_1 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $editedName, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $editedComment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $editedDate, PDO::PARAM_STR);
                $stmt->bindParam(':password', $editedPass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $editNum, PDO::PARAM_INT);
                $stmt->execute();
            }
            // 画面上に表示
            $sql = 'SELECT * FROM tb_5_1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].' ';
                echo $row['name'].' ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
            }
        } else {
        }
    ?>
</body>
</html>