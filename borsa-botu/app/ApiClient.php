<?php
// borsa-botu/app/ApiClient.php

class ApiClient {
    /**
     * Belirtilen URL'e bir GET isteği yapar ve sonucu dizi olarak döndürür.
     *
     * @param string $url İstek yapılacak tam URL.
     * @param array $headers HTTP başlıkları (isteğe bağlı).
     * @return array|null API'den dönen sonuç veya hata durumunda null.
     */
    public function get(string $url, array $headers = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 15 saniye zaman aşımı
        curl_setopt($ch, CURLOPT_USERAGENT, 'Borsa-Sinyal-Bot/1.0'); // User-agent belirtmek iyi bir pratiktir.

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            error_log("cURL Hatası: " . $error);
            return null;
        }

        if ($http_code >= 400) {
            error_log("HTTP Hatası: Kod " . $http_code . " - Yanıt: " . $response);
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * CoinGecko API'sinden kripto para için OHLC (Açılış, Yüksek, Düşük, Kapanış) verilerini çeker.
     */
    public function getCoinGeckoOhlc(string $coin_id, int $days = 90) {
        $url = "https://api.coingecko.com/api/v3/coins/{$coin_id}/ohlc?vs_currency=usd&days={$days}";
        // CoinGecko Pro API anahtarınız varsa header olarak ekleyebilirsiniz:
        // $headers = ['x_cg_pro_api_key: ' . API_KEYS['COINGECKO']];
        return $this->get($url);
    }

    /**
     * Finnhub API'sinden hisse senedi için mum verilerini çeker.
     */
    public function getFinnhubCandles(string $symbol, int $days = 90) {
        $to = time();
        $from = strtotime("-{$days} days", $to);
        $apiKey = API_KEYS['FINNHUB'];
        $url = "https://finnhub.io/api/v1/stock/candle?symbol={$symbol}&resolution=D&from={$from}&to={$to}&token={$apiKey}";
        return $this->get($url);
    }
}
?>
