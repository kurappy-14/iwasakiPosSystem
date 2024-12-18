<?php

class PrintRequest
{
    public $status = 0;
    private $timeout = 5; // 秒
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
        $xml .= '<text>◆ 印刷テスト ◆&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<text dw="false" dh="false"/>';
        $xml .= '<text>プリンター動作テスト&#10;印刷成功&#10;</text>';
        $xml .= '<feed unit="15"/>';
        $xml .= '<cut/>';
        $xml .= '</epos-print>';
        $xml .= '</s:Body>';
        $xml .= '</s:Envelope>';

        return $xml;
    }


    // プリントリクエスト送信
    public function send()
    {
        // ファイルのパスを指定して読み込む

        $jsonFilePath = '../../../setting.json';
        $jsonData = null;
        // ファイルが存在するかチェック
        if (file_exists($jsonFilePath)) {
            // ファイル内容を読み込む
            $jsonData = file_get_contents($jsonFilePath);
            // JSON形式
            $jsonData = json_decode($jsonData, true);
        } else {
            // エラーメッセージを返す
            echo json_encode(['error' => 'settings.json file not found']);
            return;
        }

        $url = 'http://'.$jsonData["environment"]["PrinterIP"].'/cgi-bin/epos/service.cgi?devid=local_printer&timeout=10000';
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

// 使用例
try {
    $printRequest = new PrintRequest();
    $status = $printRequest->send();
    echo "ステータス: " . $status;
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage();
}
