<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>mission_5-1</title>
	</head>

<body>

<?php
    #trueなら編集、falseなら投稿
    $edit_Flag = false;

	$edit_num = null;
	$edit_name = null;
    $edit_comment = null;

#データベースへの接続
    $dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'password';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
#テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS test"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "day TEXT,"
    . "pass char(18)"
	.");";
    $stmt = $pdo->query($sql);

#編集 //編集するデータの取得
    if(isset($_POST["edit"])){
        $edit_Flag = true;
        $get_pass = null;
        $input_pass = $_POST["edipass"];
        $id = $_POST["edit"];
        //表示部分
        $sql = 'SELECT * FROM test';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($id == $row["id"]){
                $get_pass = $row['pass'];
            }
        }

        if(($get_pass == "")or(($input_pass == "")&&($get_pass == ""))){
            echo "投稿時にパスワードが入力されていません"."<br />";
        }elseif($get_pass == $input_pass){
            $sql = 'SELECT * FROM test';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($_POST["edit"] == $row["id"]){
                    $edit_num = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                }
            }
        }else{
            echo "パスワードが違います"."<br />";
        }
    }
?>

<!-- 投稿フォーム -->
<!-- edit_Flag=trueならedit_がつく。falseならただのname -->
<form action = "<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php echo $edit_name; ?>" /><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $edit_comment; ?>" /><br>
        <input type="hidden" name="edit_hidden" value="<?php echo $edit_num; ?>" />
        <input type="text" name="pass" placeholder="パスワード" /><br>
	    <input type="submit" name=<?php if($edit_Flag){echo "edit_submit";} else {echo "new_submit";}?> value="送信"/><br>
    </form>
<!-- 削除番号指定フォーム -->
	<form action="<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
		 <input type="text" name="delete" placeholder="削除対象番号" /><br>
         <input type="text" name="delpass" placeholder="パスワード" /><br>
		 <input type="submit" value="削除"　/><br>
	</form>
<!-- 編集番号指定フォーム -->
    <form action="<?php print($_SERVER['SCRIPT_NAME']) ?>" method="post">
      <input type="text" name="edit" placeholder="編集対象番号"/><br>
      <input type="text" name="edipass" placeholder="  パスワード" /><br>
      <input type="submit" value="編集" />
    </form>

 <?php   
#新規投稿 insertでデータ入力
    if(isset($_POST["new_submit"])){
        if(isset($_POST["name"]) && isset($_POST["comment"])){
            $sql = $pdo -> prepare("INSERT INTO test (name, comment, day, pass) VALUES (:name, :comment, :day, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':day', $day, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $day = (string)(date("Y年m月d日 H:i:s"));
            $pass = $_POST["pass"];
            $sql -> execute();
        }else{
            echo "入力してください"."<br />";
        }
    }

#削除部分　deleteで削除
//delete部分のパスワードを「入力したときのもの」と「削除時入力するもの」で一致したとき動作するように
    if(isset($_POST["delete"])&&isset($_POST["delpass"])){
        $get_pass = null; //「投稿時入力したパス」
        $input_pass = $_POST["delpass"]; //「削除時入力するパス」
        $id = $_POST["delete"];
        #表示部分
        $sql = 'SELECT * FROM test';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($id == $row["id"]){
                $get_pass = $row["pass"];
            }
        }
        if(($get_pass == "")or(($input_pass == "")&&($get_pass == ""))){
            echo "投稿時にパスワードが入力されていません"."<br />";
        }elseif($get_pass == $input_pass){
            $sql = 'delete from test where id=:id';
            $stmt = $pdo->prepare($sql);
    	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }else{
            echo "パスワードが違います"."<br />";
        }
    }

#編集部分　
    if(isset($_POST["edit_submit"])){
        $edit_Flag = true;
    //updateで編集
    //edit部分のパスワードを「入力したときのもの」と「編集時入力するもの」で一致したとき動作するように
            $id = $_POST["edit_hidden"]; //変更する投稿番号
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $day = date("Y年m月d日 H:i:s");
            $sql = 'update test set name=:name,comment=:comment,day=:day,pass=:pass where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':day', $day, PDO::PARAM_STR);
	        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->execute();
    }

#表示部分
    $sql = 'SELECT * FROM test';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].' ';
        echo $row['day'].'<br>';
    echo "<hr>";
    } 

?>

</body>
</html>