<?php
$host = 'mysql5011.xserver.jp';
$dbname = 'xrecruit5107_recruit';
$username = 'xrecruit5107_mou';
$password = '5uoyneyvxg';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続に失敗しました: ' . $e->getMessage());
}
?>
