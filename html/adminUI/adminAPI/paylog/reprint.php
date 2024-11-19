<?php

class PrintRequest
{
    private $number;
    private $order;
    private $STORENAME = '店舗名';
    private $printerIP = '';
    public $status = 0;
    private $timeout = 5; // 秒

    public function __construct($number, $order)
    {
        if ($number === null || $order === null) {
            throw new Exception('Parameter undefined');
        } elseif (!is_array($order) || count($order) == 0 || !is_array($order[0])) {
            throw new Exception('order must be an array of associative arrays');
        }

        $this->number = $number;
        $this->order = $order;

        // ファイルのパスを指定して読み込む
        $jsonFilePath = '../../../setting.json';

        // ファイルが存在するかチェック
        if (file_exists($jsonFilePath)) {
            // ファイル内容を読み込む
            $jsonData = file_get_contents($jsonFilePath);

            // JSON形式
            $jsonData = json_decode($jsonData, true);

            // プリンターの設定
            $this->STORENAME = $jsonData['STORENAME'];
            $this->printerIP = $jsonData['environment']['PrinterIP'];
        } else {
            // エラーメッセージを返す
            echo json_encode(['error' => 'settings.json file not found']);
            return;
        }



    }

    // ePOS-XMLフォーマットの生成
    private function generateEposXml()
    {
        $xml = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
        $xml .= '<s:Body>';
        $xml .= '<epos-print xmlns="http://www.epson-pos.com/schemas/2011/03/epos-print">';
        $xml .= '<text lang="ja" />';
        $xml .= '<text font="font_a"/>';
        $xml .= '<text align="center"/>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<text dw="true" dh="true"/>';
        $xml .= '<text>'.$this->STORENAME.'&#10;</text>';
        $xml .= '<text dw="false" dh="false"/>';
        $xml .= '<text>受け渡し用控え&#10;ご来店誠にありがとうございます&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<text>------------------------------&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<text align="center"/>';
        $xml .= $this->formatArray($this->order);
        $xml .= '<feed unit="15"/>';
        $xml .= '<text>------------------------------&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<text dw="true" dh="true"/>';
        $xml .= '<text font="font_b"/>';
        $xml .= '<text>お客様のご注文番号&#10;</text>';
        $xml .= '<text width="8" height="5"/>';
        $xml .= '<text font="font_a"/>';
        $xml .= '<text reverse="false" ul="false" em="false" color="color_1"/>';
        $xml .= '<text>' . $this->number . '&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<cut/>';
        $xml .= '</epos-print>';
        $xml .= '</s:Body>';
        $xml .= '</s:Envelope>';

        return $xml;
    }

    // orderリストの整形
    private function formatArray($order)
    {
        $formatted = '';
        foreach ($order as $item) {
            $name = $item['name'];
            $count = strval($item['count']);
            $nameWidth = mb_strlen($name, 'UTF-8');
            $spacesNeeded = 30 - $nameWidth - strlen($count);
            $formatted .= '<text>' . $name . str_repeat(' ', $spacesNeeded) . $count . '点&#10;</text>';
        }
        return $formatted;
    }

    // プリントリクエスト送信
    public function send()
    {
        $url = 'http://'.$this->printerIP.'/cgi-bin/epos/service.cgi?devid=local_printer&timeout=10000';
        $xml = $this->generateEposXml();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'If-Modified-Since: Thu, 01 Jan 1970 00:00:00 GMT',
            'SOAPAction: ""'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $response = curl_exec($ch);
        $this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo "Curl error: " . curl_error($ch);
            $this->status = 0;  // ネットワークエラー扱い
        }
        curl_close($ch);

        return $this->status;
    }
}


// リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $number = $data['call_number'];
    $order = $data['products'];


    $printRequest = new PrintRequest($number, $order);
    $status = $printRequest->send();
    echo json_encode(['status' => $status]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
