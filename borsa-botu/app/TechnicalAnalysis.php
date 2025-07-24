<?php
// borsa-botu/app/TechnicalAnalysis.php

class TechnicalAnalysis {
    /**
     * Üssel Hareketli Ortalama (EMA) hesaplar.
     */
    public static function ema(array $data, int $period): ?array {
        if (count($data) < $period) return null;

        $emas = [];
        $multiplier = 2 / ($period + 1);

        // İlk EMA değeri için SMA kullan
        $initial_slice = array_slice($data, 0, $period);
        $sma = array_sum($initial_slice) / $period;

        // İlk periyot için EMA'ları hesapla (SMA'dan başlayarak)
        $emas_for_first_period = [$sma];
        for ($i = 1; $i < $period; $i++) {
            $ema = ($data[$i] - end($emas_for_first_period)) * $multiplier + end($emas_for_first_period);
            $emas_for_first_period[] = $ema;
        }

        // Kalan veriler için EMA hesapla
        $emas = $emas_for_first_period;
        for ($i = $period; $i < count($data); $i++) {
            $ema = ($data[$i] - end($emas)) * $multiplier + end($emas);
            $emas[] = $ema;
        }
        return $emas;
    }

    /**
     * Göreceli Güç Endeksi (RSI) hesaplar.
     */
    public static function rsi(array $data, int $period = 14): ?array {
        if (count($data) <= $period) return null;

        $changes = [];
        for ($i = 1; $i < count($data); $i++) {
            $changes[] = $data[$i] - $data[$i - 1];
        }

        $gains = array_map(fn($change) => $change > 0 ? $change : 0, $changes);
        $losses = array_map(fn($change) => $change < 0 ? abs($change) : 0, $changes);

        $avg_gain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avg_loss = array_sum(array_slice($losses, 0, $period)) / $period;

        $rsis = [];
        if ($avg_loss == 0) {
            $rsis[] = 100;
        } else {
            $rs = $avg_gain / $avg_loss;
            $rsis[] = 100 - (100 / (1 + $rs));
        }

        for ($i = $period; $i < count($changes); $i++) {
            $avg_gain = (($avg_gain * ($period - 1)) + $gains[$i]) / $period;
            $avg_loss = (($avg_loss * ($period - 1)) + $losses[$i]) / $period;

            if ($avg_loss == 0) {
                $rsis[] = 100;
            } else {
                $rs = $avg_gain / $avg_loss;
                $rsis[] = 100 - (100 / (1 + $rs));
            }
        }
        return $rsis;
    }

    /**
     * MACD (Moving Average Convergence Divergence) hesaplar.
     */
    public static function macd(array $data, int $fast_period = 12, int $slow_period = 26, int $signal_period = 9): ?array {
        $ema_fast = self::ema($data, $fast_period);
        $ema_slow = self::ema($data, $slow_period);

        if ($ema_fast === null || $ema_slow === null) return null;

        // EMA dizilerinin başlangıçlarını hizala
        $ema_slow_aligned = array_slice($ema_slow, count($ema_slow) - count($ema_fast));

        $macd_line = [];
        foreach ($ema_fast as $i => $value) {
            $macd_line[] = $value - $ema_slow_aligned[$i];
        }

        if (count($macd_line) < $signal_period) return null;

        $signal_line = self::ema($macd_line, $signal_period);
        $histogram = [];

        // Sinyal hattını MACD hattı ile hizala
        $macd_line_aligned = array_slice($macd_line, count($macd_line) - count($signal_line));

        foreach ($signal_line as $i => $value) {
            $histogram[] = $macd_line_aligned[$i] - $value;
        }

        return [
            'macd' => $macd_line_aligned,
            'signal' => $signal_line,
            'histogram' => $histogram
        ];
    }
}
?>
