<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    if (strlen($content) > 140) {
        die('エラー: ツイートは140文字以内で入力してください。');
    }

    $stmt = $pdo->prepare('INSERT INTO tweets (content, created_at) VALUES (:content, NOW())');
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    header('Location: ../public/index.php');
    exit;
}
?>
