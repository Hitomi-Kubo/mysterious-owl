<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" >
</head>
<body>
<?php
    //misson4-1
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, 
           array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//mission4-2
	//テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME,"
	. "password char(32)"
	.");";
	$stmt = $pdo->query($sql);
    
    //misson4-3
    //デバッグ用:「tbtest」と表示された
    /*$sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo '<br>';
	}
	echo "<hr>";*/
    
    //mission4-4
    //デバッグ用
    /*$sql ='SHOW CREATE TABLE tbtest';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>";*/
	
    //まず、設定しておくこと
    /*元のコード→最初で定義せずに条件分岐文内で定義する
    $password1=$_POST["password1"];
    $password2=$_POST["password2"];
    $password3=$_POST["password3"];
    */
    
    //投稿機能
    if(!empty($_POST["name"]) && !empty($_POST["comment"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        if(empty($_POST["editNum"])&&!empty($_POST["password1"])){
            //作成したテーブルの構成詳細を確認する
            $password1=$_POST["password1"];//パスワードの定義
	        $password=$password1;
	        $date=new DateTime();
	        $date=$date -> format('Y-m-d H:i:s');
	        $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, password) 
	                                VALUES (:name, :comment, :date, :password)");
	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> execute();
        }else{
            //編集実行機能
            $password1=$_POST["password1"];//パスワードの定義
            $id=$_POST["editNum"]; //変更する投稿番号
            $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
		         if($row['id']==$id && $password1==$row['password']){
	                 //変更したい名前、変更したいコメントは自分で決めること
	                 $sql = 'UPDATE tbtest SET name=:name, comment=:comment 
	                         WHERE id=:id';
	                 $stmt = $pdo->prepare($sql);
	                 $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	                 $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	                 $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	                 $stmt->execute();
		         }
            }
        }
    }
    
    //削除機能
    if(!empty($_POST["deleteNo"])&&!empty($_POST["password2"])){
        $password2=$_POST["password2"];//パスワードの定義
        $id=$_POST["deleteNo"];
        $sql = 'SELECT * FROM tbtest';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
		    if($password2==$row['password']){
		        $sql = 'delete from tbtest where id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt->execute();
		    }
	     }
    }
    
	 //編集選択
    if(!empty($_POST["edit"]) && !empty($_POST["password3"])){
        $password3=$_POST["password3"];//パスワードの定義
        $id=$_POST["edit"];
        $sql = 'SELECT * FROM tbtest';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
		    if($row['id']==$id && $password3==$row['password']){
		        //$rowの中にはテーブルのカラム名が入る
		        $editid=$row['id'];
		        $editname=$row['name'];
		        $editcomment=$row['comment'];
		    }    
		}
    }
?>

    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" 
        value="<?php if(isset($editname)){echo $editname;}?>">
        <br>
        <input type="text" name="comment" placeholder="コメント"
        value="<?php if(isset($editcomment)){echo $editcomment;}?>">
        <input type="hidden" name="editNum"
        value="<?php if(isset($editid)){echo $editid;}?>">
        <br>
        <input type="text" name="password1" 
        placeholder="パスワード">
        <input type="submit" name="submit" value="送信">
        <p>
        <input type="text" name="deleteNo" placeholder="削除対象番号">
        <br>
        <input type="text" name="password2" 
        placeholder="パスワード">
        <input type="submit" name="delete" value="削除">
        </p>
        <p>
        <input type="text" name="edit" placeholder="編集対象番号">
        <br>
        <input type="text" name="password3" 
        placeholder="パスワード">
        <input type="submit" name="change" value="編集">
        </p>
    </form>
    
    <?php
    //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
	$sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
		echo "<hr>";
	}
?>
</body>        
</html>