<?php
session_start();
require('../../../conf/dbconnect.php');

//セッション
$pl_id = $_SESSION['id'];

//playerの人数をカウントする
$sql = "SELECT * FROM player";
$sth = $db->query($sql);
$pl_count = $sth->rowCount();

//ゲーム開始時に武器をランダムで配布する
$item_id = ['1', '2', '3', '4'];
$r = rand(1, count($item_id));
$stmt = $db->prepare('INSERT INTO backpack SET item_id=?,pl_id=?,amount=?');
$usersArray = $stmt->fetchALL();
$stmt->execute(array($r, $pl_id, 1));

//回復アイテム
$stmt = $db->prepare('INSERT INTO backpack SET item_id=?,pl_id=?,amount=?');
$usersArray = $stmt->fetchALL();
$stmt->execute(array(6, $pl_id, 3));

$stmt = $db->prepare('UPDATE player SET pl_hp = ? WHERE pl_id = ?');
$stmt->execute(array('100',$pl_id));

header('Location: canvas.php');
exit;
?>
