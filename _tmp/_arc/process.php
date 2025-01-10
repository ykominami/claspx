<?php
// データがPOSTされたかどうかを確認
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // $_POST配列からデータを取得
  $name = $_POST["name"];
  $email = $_POST["email"];

  // データのバリデーション (例：空でないことを確認)
  if (empty($name)) {
    echo "名前が入力されていません。<br>";
  }
  if (empty($email)) {
    echo "メールアドレスが入力されていません。<br>";
  }

  // データが有効な場合の処理 (例：データベースに保存、メール送信など)
  if (!empty($name) && !empty($email)) {
    // ここでデータベースへの保存やメール送信などの処理を行う

    // 例：データを出力
    echo "名前: " . $name . "<br>";
    echo "メールアドレス: " . $email . "<br>";
    echo "データを受け取りました。";
  }
} else {
  // POSTリクエスト以外でアクセスされた場合
  echo "不正なアクセスです。";
}
?>