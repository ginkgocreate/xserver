<?php
require_once './includes/db.php';

// ツイート一覧を取得
$stmt = $pdo->query('SELECT * FROM tweets ORDER BY retweet_flag DESC, created_at DESC');
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <title>ホーム</title>
    <style>
        title {
            text-align: center;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
        }

        .tweet {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }

        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
        }

        /* タイトルを中央寄せ */
        h1,
        .modal-header h2 {
            text-align: center;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        /* モーダル全体のデザイン */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            /* 少し暗くする */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            /* 他の要素より前面に */
        }

        /* モーダルコンテンツのデザイン */
        .modal-content {
            background: linear-gradient(135deg, #ffffff, #f3f3f3);
            /* グラデーション背景 */
            padding: 20px;
            border-radius: 10px;
            /* 角丸 */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            /* 影 */
            width: 90%;
            max-width: 400px;
        }

        /* モーダル内のヘッダー */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        /* モーダルを閉じるボタン */
        .close {
            cursor: pointer;
            font-size: 24px;
            color: #555;
            transition: color 0.3s ease;
            /* ホバーで色を変更 */
        }

        .close:hover {
            color: #000;
        }

        /* フォーム内のボタン */
        .modal-content button {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 0;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .modal-content button:hover {
            background: #0056b3;
            /* ホバー時に色を変更 */
        }

        /* フォーム内のテキストエリア */
        .modal-content textarea {
            width: 94%;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            resize: none;
            /* サイズ変更を禁止 */
            font-size: 14px;
            margin-top: 10px;
        }

        /* ツイートモーダル */
        #tweetModal  {
            display: none;
        }

        /* ツイート全体の中央寄せ */
        .tweet-container {
            display: flex;
            justify-content: center;
            /* 中央寄せ */
            margin-bottom: 20px;
            /* ツイート間のスペース */
        }

        /* ツイートボックスのデザイン */
        .tweet-box {
            width: 80%;
            /* ツイートボックスの幅 */
            max-width: 500px;
            /* 最大幅 */
            padding: 15px;
            border: 1px solid #ccc;
            /* 枠線 */
            border-radius: 8px;
            /* 角丸 */
            background-color: #f9f9f9;
            /* 背景色 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* 影 */
            text-align: center;
            /* テキストの中央寄せ */
        }

        /* ツイート本文のデザイン */
        .tweet-content {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        /* ボタンのスペース */
        .tweet-box i {
            margin: 0 5px;
            /* ボタン間のスペース */
        }
    </style>
    <script>
        // いいね・RTの制御
        function toggleFlag(tweetId, flagType) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', './scripts/update_flag.php');
            xhr.setRequestHeader('Content-Type', 'application/json');

            // リクエストデータを作成
            const requestData = JSON.stringify({
                tweetId: tweetId,
                flagType: flagType
            });

            xhr.onload = () => {
                try {
                    // レスポンスをJSONとしてパース
                    const data = JSON.parse(xhr.responseText);

                    // レスポンスが成功の場合
                    if (data.newStatus !== undefined) {
                        const icon = document.getElementById(`${flagType}-${tweetId}`);
                        if (data.newStatus === 1) {
                            if (flagType === 'like') {
                                icon.className = 'fas fa-heart';
                                icon.style.color = 'red';
                            } else if (flagType === 'retweet') {
                                icon.style.color = 'green';
                            }
                        } else {
                            if (flagType === 'like') {
                                icon.className = 'far fa-heart';
                                icon.style.color = 'gray';
                            } else if (flagType === 'retweet') {
                                icon.style.color = 'gray';
                            }
                        }
                        // 再読み込み
                        location.reload();
                    } else if (data.error) {
                        console.error('サーバーエラー:', data.error);
                    }
                } catch (error) {
                    console.error('レスポンスの解析に失敗しました:', xhr.responseText);
                }
            };

            xhr.onerror = () => {
                console.error('リクエストエラーが発生しました');
            };

            // JSONデータを送信
            xhr.send(requestData);
        }
        // ツイートモーダルを開く
        function openModal() {
            document.getElementById('tweetModal').style.display = 'flex';
        }
        // ツイートモーダルを消す
        function closeModal() {
            document.getElementById('tweetModal').style.display = 'none';
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>ホーム</h1>
        <?php foreach ($tweets as $tweet): ?>
            <div class="tweet-container">
                <div class="tweet-box">
                    <p class="tweet-content"><?= htmlspecialchars($tweet['content']) ?></p>
                    <!-- Likeボタン -->
                    <i id="like-<?= $tweet['id'] ?>" class="<?= $tweet['like_flag'] ? 'fas' : 'far' ?> fa-heart"
                        style="color: <?= $tweet['like_flag'] ? 'red' : 'gray' ?>; cursor: pointer;"
                        onclick="toggleFlag(<?= $tweet['id'] ?>, 'like')"></i>
                    <!-- Retweetボタン -->
                    <i id="retweet-<?= $tweet['id'] ?>" class="fas fa-retweet"
                        style="color: <?= $tweet['retweet_flag'] ? 'green' : 'gray' ?>; cursor: pointer;"
                        onclick="toggleFlag(<?= $tweet['id'] ?>, 'retweet')"></i>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="fab" onclick="openModal()">+</button>

        <!-- モーダル -->
        <div id="tweetModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>ツイート</h2>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <form action="./scripts/tweet.php" method="POST">
                    <textarea name="content" rows="5" maxlength="140" required placeholder="今何してる？"></textarea>
                    <button type="submit">ツイート</button>
                </form>
            </div>
        </div>
</body>

</html>