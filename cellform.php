<?php
session_start();
require('../../../conf/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTデータからcellIdを取得
    $cellId = $_POST['cellId'] ?? '';

    //プレイヤーidをsessionから$idに入れる
    $pl_id = $_SESSION['id'];

    //セルidがあるかつ、pl_idがnullではないときにposをupdateする
    if ($cellId) {
        if ($pl_id !== null) {
            $stmt = $db->prepare('UPDATE player SET pos = ? WHERE pl_id = ?');
            $stmt->execute(array($cellId, $pl_id));
        }
    }

    //プレイヤーidごとにログファイルを作成
    //chmodで権限を755,644
    //ディレクトリをあらかじめ作成
    if ($pl_id !== null) {
        //プレイヤーidごとにテキストファイルパスを設定(player/player_1.txt)
        $filePath = "pl_log/player_" . $pl_id . ".txt";

        //書き込む内容を作成
        $content = $pl_id . "は" . $cellId . "に移動しました\n";

        //ファイルが存在する場合は上書き保存、存在しない場合は新規作成
        $fileHandle = fopen($filePath, 'a'); //aは上書き保存

        if ($fileHandle) {
            //ファイルに内容を書き込む
            fwrite($fileHandle, $content);
            fclose($fileHandle);
        } else {
            echo "ファイルの書き込みに失敗しました";
        }
    } else {
        echo "プレイヤーIDが不明です";
    }

    $_SESSION['roop'] = 1;
    // 処理が終わったら元のHTMLにリダイレクト
    header("Location:canvas.php");
    exit(); // スクリプトを終了
}