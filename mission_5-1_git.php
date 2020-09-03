<!DOCTYPE html>
<html lang=ja>
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    
    <span style="font-size:25px;">McDonald'sの略し方は？</span><br>

    <?php

        //////////////////////////////////////////        

        //データベースへの接続
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

        //テーブルの作成
        $sql = "CREATE TABLE IF NOT EXISTS tb_5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "compas TEXT,"
        . "date TEXT"
        . ");";
        $stmt = $pdo->query($sql);

        /////////////////////////////////////////

        //受信した値をそれぞれ変数にする

        $date = date("Y/m/d H:i:s");    //時間の取得&変数化
        
        if(!empty($_POST["submit"])){       //送信ボタンが押されたとき
            $name = $_POST["name"];         //コメ主名
            $comment = $_POST["comment"];   //コメント
            $compas = $_POST["compas"];     //パスワード
            $editnumhol = $_POST["editnumhol"]; //編集モードの時編集番号が入る
        }

        if(!empty($_POST["delsub"])){       //削除ボタンが押された時
            $delnum = $_POST["delnum"];     //削除番号
            $delpas = $_POST["delpas"];     //パスワード(コメントのパスワードと後で比較)
        }

        if(!empty($_POST["editsub"])){      //編集ボタンが押された時
            $editnum = $_POST["editnum"];   //編集番号
            $editpas = $_POST["editpas"];   //パスワード(コメントのパスワードと後で比較)
        }


        //////////////////////////////////////////
        
        //削除番号が送信されたらその行を削除する
        
        if(!empty($delnum)){  
            $sql = 'SELECT * FROM tb_5 WHERE id=:id';
            $stmt = $pdo->prepare($sql);                  
            $stmt->bindParam(':id', $delnum, PDO::PARAM_INT); 
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                if ($delpas == $row['compas']){
                    $sql = 'DELETE FROM tb_5 WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $delnum, PDO::PARAM_INT);
                    $stmt->execute();
                }else{
                    echo "パスワードが違います<br>";
                }
            }  
        }

        ///////////////////////////////////////////

        //編集番号が送信されたら、番号の名前とコメを探して変数に代入する(あとでフォームの初期値へ)

        if(!empty($editnum)){ //編集番号が送信されている時
            $sql = 'SELECT * FROM tb_5 WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  
            $stmt->bindParam(':id', $editnum, PDO::PARAM_INT); 
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                if ($editpas == $row['compas']){
                    $editnum_form = $row['id'];
                    $editname_form = $row['name'];
                    $editcom_form = $row['comment'];
                    $editpas_form = $row['compas'];
                }else{
                    echo "パスワードが違います<br>";
                }
            }
        }
        
        //////////////////////////////////////////
        
        //コメントが送信された時編集番号があればその行を書き直し、なければ追加

        if(isset($name) && isset($comment)){    //名前とコメントが送信された時

            //編集番号が送信されたなら編集モード
            if(is_numeric($editnumhol)){
                $sql = 'UPDATE tb_5 SET name=:name,comment=:comment,compas=:compas,date=:date WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':compas', $compas, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $editnumhol, PDO::PARAM_INT);
                $stmt->execute();

            //編集番号が送信されてないなら追記モード
            }else{    
                $sql = $pdo -> prepare("INSERT INTO tb_5 (name, comment, compas, date) VALUES (:name, :comment, :compas, :date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':compas', $compas, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> execute();
            }
        }
    ?>
        

    <!--フォーム欄-------------------------------->

    <!--投稿用-->
    <form action=""method="post">
        <input type="text"name="name"placeholder="名前"value="<?php if(!empty($editname_form)){echo $editname_form;}?>">
        <br>
        <input type="text"name="comment"placeholder="コメント"value="<?php if(!empty($editcom_form)){echo $editcom_form;} ?>">
        <br>
        <input type="text"name="compas"placeholder="パスワード"value="<?php if(!empty($editpas_form)){echo $editpas_form;} ?>">
        <input type="hidden"name="editnumhol"placeholder=""value="<?php if(!empty($editnum_form)){echo $editnum_form;} ?>">
        <input type="submit"name="submit">
    </form>
    
    <br>
    
    <!--削除用-->
    <form action=""method="post">
        <input type="number"min="1"name="delnum"placeholder="削除する投稿番号">
        <br>
        <input type="text"name="delpas"placeholder="パスワード">
        <input type="submit"name="delsub"value="削除">
    </form>
    
    <br>
    
    <!--編集用-->
    <form action=""method="post">
        <input type="number"min="1"name="editnum"placeholder="編集する投稿番号">
        <br>
        <input type="text"name="editpas"placeholder="パスワード">
        <input type="submit"name="editsub"value="編集">
    </form>
    
    <!------------------------------------------------>

    <?php
        $sql = 'SELECT * FROM tb_5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
            echo '<hr>';
        }
    ?>
</body>
</html>