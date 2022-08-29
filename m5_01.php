<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <?php
        
        $name= $_POST["name"];
        $comment=$_POST["comment"];
        $pass = $_POST["pass"];
        $delete=$_POST["delete"];
        $edit=$_POST["edit"];
        $edit_n = $_POST["edit_n"];
        $pass_d = $_POST["pass_d"];
        $pass_e = $_POST["pass_e"];
        $date = date("Y/m/d G:i:s");
        $edit_num = (int)$edit_n;
       
        //データベースに接続
        $dsn = 'データベース名';
        $user = 'ユーザ名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS mytb"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "pass char(32)"
        .");";
        $stmt = $pdo->query($sql);
        
        //データベース読み込み&書き込み
        if(!empty($name) && !empty($comment) && !empty($pass) && empty($edit_num)){
            $sql = $pdo -> prepare("INSERT INTO mytb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> execute();
            //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
        }
        
        //編集
        if(!empty($edit) && !empty($pass_e)){
            $sql = 'SELECT * FROM mytb';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                if($row[0] == $edit && $row[4] == $pass_e){
        		    $ediNumber = $row[0];
        		    $newname = $row[1];
        		    $newcoment = $row[2];
                    $newpass = $row[4];   
        		}
            }
        }elseif(!empty($name) && !empty($comment)&& !empty($pass) && !empty($edit_num)){
            $id = $edit_n; //変更する投稿番号
            $name= $_POST["name"];
            $comment=$_POST["comment"]; //変更したい名前、変更したいコメントは自分で決めること
            $pass = $_POST["pass"];
            
            $sql = 'UPDATE mytb SET name=:name,comment=:comment,pass=:pass WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
        }
        
        //削除
        if(!empty($delete) && !empty($pass_d)){
            $sql = 'SELECT * FROM mytb';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                if ($row[0] == $delete && $row[4] == $pass_d){
                    $id = $delete; //変更する投稿番号
                    $sql = 'delete from mytb where id=:id';
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                } 
            }
        }
        
        //内容を表示
        $sql = 'SELECT * FROM mytb';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].' ';
            echo $row['pass'].' ';
            echo "<br>";
        echo "<hr>";
        }
        
    ?>
    
    <form action="" method="post">
        名前：　　　<input type="text" name="name" value="<?php  if(!empty($newname)){echo $newname;} ?>">
        <br>
        コメント：　<input type="text" name="comment" value="<?php if(!empty($newcoment)){echo $newcoment;} ?>">
        <br>
        パスワード：<input type="text" name="pass" value="<?php if(!empty($newpass)){echo $newpass;} ?>">
        <input type="submit" name="submit">
        <br>
        <input type="number" name="delete" placeholder="削除対象番号">
        <input type="text" name="pass_d" placeholder="パスワード入力">
        <input type="submit" name="submit" value="削除">
        <br>
        <input type="number" name="edit" placeholder="編集対象番号">
        <input type="text" name="pass_e" placeholder="パスワード入力">
        <input type="submit" name="submit" value="編集">
        <input  type="hidden" name="edit_n" value="<?php echo $ediNumber; ?>">
    </form>
</body>    