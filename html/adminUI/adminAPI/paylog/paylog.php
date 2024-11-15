<?php
// コンテンツタイプをjsonに
header("Content-Type: application/json; charset=UTF-8");

// データベース接続情報
$servername = "mariaDB";
$username = "user";
$password = "password";
$dbname = "exampledb";

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQLクエリ
$sql = "
    SELECT 
        o.order_id,
        SUM(p.price * pu.quantity) AS total_price,
        o.order_date AS order_time , o.provide_status,o.payment_type
    FROM 
        orders o
    JOIN 
        purchase pu ON o.order_id = pu.order_id
    JOIN 
        products p ON pu.product_code = p.product_code
    GROUP BY 
        o.order_id, o.order_date
";

// クエリの実行
$result = $conn->query($sql);

// 結果を格納するための配列
$response = ["sum" => 0, "data" => []];

if ($result->num_rows > 0) {
    // 各order_idごとにデータを格納
    while ($row = $result->fetch_assoc()) {
        $orderData = [
            "order_id" => $row["order_id"],
            "price" => (int) $row["total_price"],
            "time" => $row["order_time"],
            "status" => $row["provide_status"],
            "payment_type" => $row["payment_type"]
        ];

        // データ配列に追加
        $response["data"][] = $orderData;

        // 全ての合計を計算(provide_statusが1と-1のもの以外)]
        if ($row["provide_status"] != 1 && $row["provide_status"] != -1) {
            $response["sum"] += (int) $row["total_price"];

            // payment_typeごとの合計を計算
            if (!isset($response["payment_type"][$row["payment_type"]]))
                $response["payment_type"][$row["payment_type"]] = 0;
            $response["payment_type"][$row["payment_type"]] += (int) $row["total_price"];
        }



    }
}

// JSONとして出力
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// データベース接続を閉じる
$conn->close();
?>