<!-- サンプルコード -->
<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>通報</title>
    </head>

    <body>
 
        <?php 
        $key1=$_GET['key1'];/*key1受け取り*/

        if(!isset($_POST['key2'])){ /*key2の生存チェック*/
            echo "エラー keyがありません";
        }else{
            $key2=$_POST['key2'];/*key2受け取り*/
            $filename=$_POST['file'];/*URL用ファイル名受け取り*/
 
            if(strcmp("$key1","$key2")==0){ /*keyの一致チェック*/
                echo "<h1>通報しました</h1>\n";
                

                /*データベース接続*/
                $db = new mysqli('localhost','sdv2019a','HIbIbymQ2SAH','SDV2019A');
	            if($db->connect_error) {
		            echo $db->connect_error;
		            exit ();
                }
                
                
                /*データベースに状態の書き換え*/
	            $sql = 'UPDATE NoticeTable SET Status = 8 WHERE Web = "'.$filename.'"';/*ステータスを書き換え*/
                $result = mysqli_query($db,$sql);
                
				$sql = 'SELECT Mail FROM `HumanTable` WHERE (Id LIKE "__1___" OR Id LIKE "__4___") AND Mail !=""';
				$result = mysqli_query($db,$sql);
                $i=1;
                while ($data = $result->fetch_assoc()){
				    /*アドレス読み込み*/
                    $to=array_shift($data);/*協力者、市役所職員メールアドレス*/
                    $subject = "捜索終了"; /*件名*/
                    $message = "捜索が終了になりました。"; /*内容*/
                    $headers = "From: testsal"; /*差出人*/
                    
                    if (mb_send_mail($to,$subject,$message,$headers)){
                        echo $i.'送信成功<br>';
                    } else {
                        echo $i.'送信失敗<br>';
                    }
                    $i=$i+1;
                }
                
                $sql = 'SELECT Mail FROM NoticeTable AS N,HumanTable AS H, RWTable AS R WHERE N.ReceiverId = R.ReceiverId AND R.WatcherId = H.Id AND N.Web = "'.$filename.'" AND Mail != ""';/*対象の介護士のメールアドレス読み込み*/
                $result = mysqli_query($db,$sql);
                $i=1;
                while ($data = $result->fetch_assoc()){
                    
                    /*アドレス読み込み*/
                    $to=array_shift($data);/*対象介護士メールアドレス*/
                    $subject = "捜索終了"; /*件名*/
                    $message = "捜索が終了になりました。"; /*内容*/
                    $headers = "From: testsal"; /*差出人*/
                    
                    if (mb_send_mail($to,$subject,$message,$headers)){
                        echo $i.'送信成功<br>';
                    } else {
                        echo $i.'送信失敗<br>';
                    }
                    $i=$i+1;
                }
                
                /*データベース切断*/
                $db = mysqli_close($db);
	            if(!$db){
		            exit('データベースとの接続が閉じられませんでした。');
	            }
	  
            }else{
                echo "エラー keyが一致しません\n";
            }
        }
        ?>
 
    </body>
</html>