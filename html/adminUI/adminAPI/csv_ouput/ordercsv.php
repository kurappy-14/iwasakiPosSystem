<?php
// データベース接続情報
$host = "mariaDB";
$dbname = "exampledb";
$username = "user";
$password = "password";

// データベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// 出力したいSQLクエリ
$sql = "select * from orders";  // ここを実行したいSQLに置き換える
$stmt = $pdo->prepare($sql);
$stmt->execute();

// ファイル名を指定してヘッダ情報を設定（CSV出力用）
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="ordersdb.csv"');

// 出力バッファを開く
$output = fopen('php://output', 'w');

// カラム名（ヘッダー）をCSVに書き出す
$columns = array_keys($stmt->fetch(PDO::FETCH_ASSOC)); // 最初の行からカラム名を取得
fputcsv($output, $columns);

// データを再度取得し、CSVに書き出す
$stmt->execute(); // もう一度クエリを実行してデータを取得
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

// ファイルポインタを閉じる
fclose($output);
exit();
?>
