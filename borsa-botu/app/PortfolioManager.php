<?php
// borsa-botu/app/PortfolioManager.php

class PortfolioManager {
    private $conn;
    private $portfolio_id;

    public function __construct(PDO $db_connection, int $portfolio_id) {
        $this->conn = $db_connection;
        $this->portfolio_id = $portfolio_id;
    }

    /**
     * Bir hisse senedi alım işlemi gerçekleştirir.
     */
    public function buy(int $stock_id, float $quantity, float $price) {
        $this->conn->beginTransaction();
        try {
            $this->addTransaction($stock_id, 'BUY', $quantity, $price);
            $this->updatePosition($stock_id, $quantity, $price, 'BUY');
            $this->updateBalance($quantity * $price, 'DECREASE');
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Alım işlemi hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bir hisse senedi satım işlemi gerçekleştirir.
     */
    public function sell(int $stock_id, float $quantity, float $price) {
        $this->conn->beginTransaction();
        try {
            $position = $this->getPosition($stock_id);
            if (!$position || $position['quantity'] < $quantity) {
                throw new Exception("Satış için yeterli pozisyon yok.");
            }
            $this->addTransaction($stock_id, 'SELL', $quantity, $price);
            $this->updatePosition($stock_id, $quantity, $price, 'SELL');
            $this->updateBalance($quantity * $price, 'INCREASE');
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Satış işlemi hatası: " . $e->getMessage());
            return false;
        }
    }

    private function addTransaction(int $stock_id, string $type, float $quantity, float $price) {
        $sql = "INSERT INTO transactions (portfolio_id, stock_id, transaction_type, quantity, price, transaction_date) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->portfolio_id, $stock_id, $type, $quantity, $price]);
    }

    private function updatePosition(int $stock_id, float $quantity, float $price, string $type) {
        $position = $this->getPosition($stock_id);
        if ($position) {
            if ($type === 'BUY') {
                $new_quantity = $position['quantity'] + $quantity;
                $new_avg_cost = (($position['average_cost'] * $position['quantity']) + ($price * $quantity)) / $new_quantity;
            } else {
                $new_quantity = $position['quantity'] - $quantity;
                $new_avg_cost = $position['average_cost'];
            }

            if ($new_quantity > 0.00000001) { // Kayan nokta hatalarını önlemek için küçük bir eşik
                $sql = "UPDATE positions SET quantity = ?, average_cost = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$new_quantity, $new_avg_cost, $position['id']]);
            } else {
                $sql = "DELETE FROM positions WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$position['id']]);
            }
        } elseif ($type === 'BUY') {
            $sql = "INSERT INTO positions (portfolio_id, stock_id, quantity, average_cost) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$this->portfolio_id, $stock_id, $quantity, $price]);
        }
    }

    private function updateBalance(float $amount, string $direction) {
        $op = $direction === 'INCREASE' ? '+' : '-';
        $sql = "UPDATE portfolios SET current_balance = current_balance {$op} ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$amount, $this->portfolio_id]);
    }

    public function getPosition(int $stock_id) {
        $sql = "SELECT * FROM positions WHERE portfolio_id = ? AND stock_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->portfolio_id, $stock_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
