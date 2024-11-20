<?php
require_once '../includes/db.php';

// ツイート一覧を取得
$stmt = $pdo->query('SELECT * FROM tweets ORDER BY created_at DESC');
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; }
        .tweet { border-bottom: 1px solid #ddd; padding: 10px; }
        .fab { position: fixed; bottom: 20px; right: 20px; background: #007bff; color: #fff; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 24px; cursor: pointer; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 5px; width: 90%; max-width: 400px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; }
        .close { cursor: pointer; font-size: 24px; }
    </style>
    <script>
        function openModal() {
            document.getElementById('tweetModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('tweetModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>ホーム</h1>
        <?php foreach ($tweets as $tweet): ?>
            <div class="tweet">
                <p><?= htmlspecialchars($tweet['content']) ?></p>
                <small><?= $tweet['created_at'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="fab" onclick="openModal()">+</button>

    <!-- モーダル -->
    <div id="tweetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>ツイート</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form action="../scripts/post_tweet.php" method="POST">
                <textarea name="content" rows="5" maxlength="140" style="width: 100%;" required></textarea>
                <button type="submit" style="margin-top: 10px;">ツイート</button>
            </form>
        </div>
    </div>
</body>
</html>
