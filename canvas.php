<?php
session_start();
require('../../../conf/dbconnect.php');

date_default_timezone_set('Asia/Tokyo');
$time = date("H:i:s");

echo $time;


//プレイヤーidをセッションから取得
$pl_id = $_SESSION['id'] ?? null;

//デバッグ
//var_dump($playerPos);

//プレイヤー座標を取得
$pl_pos = $db->prepare('SELECT pos FROM player WHERE pl_id = ?');
$pl_pos->execute(array($pl_id));
$playerPos = $pl_pos->fetch(); //プレイヤーの座標
$playerPos = $playerPos['pos'];

//var_dump($playerPos);

if (!isset($_SESSION['roop'])) {
    // プレイヤーの座標がまだ設定されていない場合、ランダムな座標を設定
    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']; //横軸
    $rows = [1, 2, 3, 4, 5, 6, 7]; //縦軸

    $randomX = $columns[array_rand($columns)]; //横軸をランダムに選択
    $randomY = $rows[array_rand($rows)]; //縦軸をランダムに選択
    $playerPos = $randomX . $randomY;

    //ランダムな座標をデータベースに登録
    $stmt = $db->prepare('UPDATE player SET pos = ? WHERE pl_id = ?');
    $stmt->execute(array($playerPos, $pl_id));
}

if ($pl_id !== null) {
    //プレイヤーごとのテキストファイルパスを設定
    $filePath = "pl_log/player_" . $pl_id . ".txt";

    //ファイルが存在すれば読み込む
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
    } else {
        //ファイルが存在しない場合
        $fileContent = "まだ行動した履歴がありません";
    }
} else {
    //プレイヤーが不明な場合
    $fileContent = "プレイヤーIDが不明です";
}

try {
    $stmt = $db->prepare('SELECT b.item_id, b.amount, i.item_name, i.pic FROM backpack b JOIN item i ON b.item_id = i.item_id WHERE b.pl_id = ?');
    $stmt->execute(array($pl_id));
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '接続エラー: ' . $e->getMessage();
}

try {
    //dangerエリアを登録
    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']; //横軸
    $rows = [1, 2, 3, 4, 5, 6, 7]; //縦軸

    $randomX = $columns[array_rand($columns)]; //横軸をランダムに選択
    $randomY = $rows[array_rand($rows)]; //縦軸をランダムに選択
    $area = $randomX . $randomY;

    $stmt = $db->prepare('INSERT INTO danger (area,num) VALUE (?,0)');
    $stmt->execute(array($area));
} catch (PDOException $e) {
    echo '接続エラー: ' . $e->getMessage();
}

try {
    //登録したdangerエリアをすべて取得
    $stmt = $db->prepare('SELECT area FROM danger WHERE num = 0');
    $stmt->execute();
    $dangerAreas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '接続エラー:' . $e->getMessage();
}

// 取得したエリアをJavaScript用に整形
$dangerCells = array_map(function ($row) {
    return $row['area'];
}, $dangerAreas);
$dangerCellsJson = json_encode($dangerCells);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="refresh" content="30; URL=http://gameg.s322.xrea.com/s2g3/game/canvas.php">
    <title>タイトル</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="cotainer">
        <form id="cellForm" action="cellform.php" method="POST">
            <input type="hidden" name="cellId" id="cellId">
            <div class="board">
                <!-- 56マスの生成 -->
                <div class="cell" id="A1"></div>
                <div class="cell" id="B1"></div>
                <div class="cell" id="C1"></div>
                <div class="cell" id="D1"></div>
                <div class="cell" id="E1"></div>
                <div class="cell" id="F1"></div>
                <div class="cell" id="G1"></div>
                <div class="cell" id="H1"></div>
                <div class="cell" id="A2"></div>
                <div class="cell" id="B2"></div>
                <div class="cell" id="C2"></div>
                <div class="cell" id="D2"></div>
                <div class="cell" id="E2"></div>
                <div class="cell" id="F2"></div>
                <div class="cell" id="G2"></div>
                <div class="cell" id="H2"></div>
                <div class="cell" id="A3"></div>
                <div class="cell" id="B3"></div>
                <div class="cell" id="C3"></div>
                <div class="cell" id="D3"></div>
                <div class="cell" id="E3"></div>
                <div class="cell" id="F3"></div>
                <div class="cell" id="G3"></div>
                <div class="cell" id="H3"></div>
                <div class="cell" id="A4"></div>
                <div class="cell" id="B4"></div>
                <div class="cell" id="C4"></div>
                <div class="cell" id="D4"></div>
                <div class="cell" id="E4"></div>
                <div class="cell" id="F4"></div>
                <div class="cell" id="G4"></div>
                <div class="cell" id="H4"></div>
                <div class="cell" id="A5"></div>
                <div class="cell" id="B5"></div>
                <div class="cell" id="C5"></div>
                <div class="cell" id="D5"></div>
                <div class="cell" id="E5"></div>
                <div class="cell" id="F5"></div>
                <div class="cell" id="G5"></div>
                <div class="cell" id="H5"></div>
                <div class="cell" id="A6"></div>
                <div class="cell" id="B6"></div>
                <div class="cell" id="C6"></div>
                <div class="cell" id="D6"></div>
                <div class="cell" id="E6"></div>
                <div class="cell" id="F6"></div>
                <div class="cell" id="G6"></div>
                <div class="cell" id="H6"></div>
                <div class="cell" id="A7"></div>
                <div class="cell" id="B7"></div>
                <div class="cell" id="C7"></div>
                <div class="cell" id="D7"></div>
                <div class="cell" id="E7"></div>
                <div class="cell" id="F7"></div>
                <div class="cell" id="G7"></div>
                <div class="cell" id="H7"></div>
            </div>

            <div class="log">
                <h2>ログ</h2>
                <p><?php echo nl2br(htmlspecialchars($fileContent)); ?></p>
            </div>

        </form>

        <h1>プレイヤーインベントリ</h1>

        <div class="inventory">
            <!-- インベントリの格子（7x12のマス） -->
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>
            <div class="inventory-cell"></div>

            <!-- アイテム格子に画像を埋め込む -->
            <?php foreach ($items as $item): ?>
                <div class="inventory-cell" data-item-id="<?php echo htmlspecialchars($item['item_id']); ?>">
                    <img src="./icon/<?php echo htmlspecialchars($item['pic']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 使用するアイテムを確認するポップアップ -->
        <div id="use-item-popup" class="popup" style="display:none;">
            <p>このアイテムを使用しますか？</p>
            <button id="use-item-btn">使用する</button>
            <button id="close-popup-btn">キャンセル</button>
        </div>



    </div>

    <script>
        //PHPからプレイヤーの現在座標を取得
        const playerPos = '<?php echo htmlspecialchars($playerPos); ?>';

        // PHPから取得したランダムに登録された危険なエリアをJavaScriptに渡す
        const dangerCells = <?php echo $dangerCellsJson; ?>;

        //プレイヤーの現在座標を横（A-H）と縦（1-7）に分ける
        const currentX = playerPos.charAt(0); //横
        const currentY = parseInt(playerPos.charAt(1)); //縦

        //横軸（A-H）と縦軸（1-7）の隣接セルを計算
        const adjacentCells = [];
        const directions = [
            [-1, 0],
            [1, 0], //左右
            [0, -1],
            [0, 1], //上下
            [-1, -1],
            [1, 1], //左上、右下
            [-1, 1],
            [1, -1] //右上、左下
        ];

        //A-H と 1-7 の範囲を定義
        const columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        const rows = [1, 2, 3, 4, 5, 6, 7];

        //隣接セルを計算
        directions.forEach(([dx, dy]) => {
            const newX = columns.indexOf(currentX) + dx;
            const newY = currentY + dy;

            if (newX >= 0 && newX < columns.length && newY >= 1 && newY <= 7) {
                adjacentCells.push(columns[newX] + newY);
            }
        });


        //すべてのセルを取得
        const cells = document.querySelectorAll('.cell');

        //プレイヤーがいる座標を黄色に変更
        document.getElementById(playerPos).classList.add('highlight');

        // 取得した危険なエリアに基づいて赤色に変更
        cells.forEach(cell => {
            if (dangerCells.includes(cell.id)) {
                cell.style.backgroundColor = 'red'; // 赤色に変更
            }
        });

        //すべてのセルにクリック可能な状態を適用
        cells.forEach(cell => {
            const cellId = cell.id;
            if (adjacentCells.includes(cellId)) {
                cell.classList.add('moveable'); //隣接セルをハイライトして光らせる
                cell.style.cursor = 'pointer'; //隣接セルを選択可能に
                cell.addEventListener('click', function() {
                    //クリックされたセルの処理
                    document.getElementById('cellId').value = cellId;
                    document.getElementById('cellForm').submit();
                });
            } else {
                //隣接セルでない場合はクリックできないように
                cell.style.cursor = 'not-allowed';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const inventoryCells = document.querySelectorAll('.inventory-cell');
            const popup = document.getElementById('use-item-popup');
            const closePopupBtn = document.getElementById('close-popup-btn');
            const useItemBtn = document.getElementById('use-item-btn');
            let selectedItemId = null;

            // アイテム画像がクリックされたとき
            inventoryCells.forEach(cell => {
                cell.addEventListener('click', (e) => {
                    const itemId = cell.getAttribute('data-item-id');
                    selectedItemId = itemId; // 選択されたアイテムIDを保持

                    // ポップアップを表示
                    popup.style.display = 'flex';
                });
            });

            // ポップアップのキャンセルボタンをクリックしたとき
            closePopupBtn.addEventListener('click', () => {
                popup.style.display = 'none';
            });

            // ポップアップの使用ボタンをクリックしたとき
            useItemBtn.addEventListener('click', () => {
                if (selectedItemId) {
                    // アイテムを使用する処理（ここにDB更新や処理を追加する）
                    alert('アイテム ' + selectedItemId + ' を使用しました');

                    // 使用したアイテムに対する処理を行う
                    // 例：アイテムをインベントリから削除、またはDBに更新する

                    // ポップアップを非表示にする
                    popup.style.display = 'none';
                }
            });

            // ポップアップ外のクリックで閉じる
            window.addEventListener('click', (e) => {
                if (e.target === popup) {
                    popup.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>