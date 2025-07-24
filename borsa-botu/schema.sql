-- Borsa Sinyal Botu için Veritabanı Şeması
-- Bu komutlar, Plesk panel üzerinden phpMyAdmin veya benzeri bir araçla çalıştırılmalıdır.

-- Ayarlar tablosu (Gelecekteki ayarlar için)
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- İzlenecek hisse senetleri ve kripto paralar
CREATE TABLE IF NOT EXISTS `stocks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `symbol` VARCHAR(20) NOT NULL UNIQUE,
    `exchange` VARCHAR(50) DEFAULT 'NASDAQ',
    `name` VARCHAR(255) DEFAULT NULL,
    `type` VARCHAR(20) DEFAULT 'stock' COMMENT "'stock' veya 'crypto'",
    `is_active` BOOLEAN DEFAULT TRUE COMMENT "Cron job'da takip edilip edilmeyeceği",
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Üretilen sinyaller tablosu
CREATE TABLE IF NOT EXISTS `signals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `stock_id` INT NOT NULL,
    `signal_type` ENUM('BUY', 'SELL', 'HOLD') NOT NULL,
    `price` DECIMAL(18, 8) NOT NULL,
    `signal_date` DATETIME NOT NULL,
    `ai_commentary` TEXT DEFAULT NULL,
    `indicators` JSON DEFAULT NULL COMMENT "Sinyal anındaki gösterge değerleri",
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`stock_id`) REFERENCES `stocks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cüzdan (Portfolio) Tabloları (Opsiyonel kullanım için)

CREATE TABLE IF NOT EXISTS `portfolios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `initial_balance` DECIMAL(20, 8) DEFAULT 10000.00,
    `current_balance` DECIMAL(20, 8),
    `is_active` BOOLEAN DEFAULT FALSE COMMENT "Bu cüzdana otomatik işlem yapılıp yapılmayacağı",
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `positions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `portfolio_id` INT NOT NULL,
    `stock_id` INT NOT NULL,
    `quantity` DECIMAL(20, 8) NOT NULL,
    `average_cost` DECIMAL(20, 8) NOT NULL,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`stock_id`) REFERENCES `stocks`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `portfolio_stock_unique` (`portfolio_id`, `stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `portfolio_id` INT NOT NULL,
    `stock_id` INT NOT NULL,
    `transaction_type` ENUM('BUY', 'SELL') NOT NULL,
    `quantity` DECIMAL(20, 8) NOT NULL,
    `price` DECIMAL(18, 8) NOT NULL,
    `transaction_date` DATETIME NOT NULL,
    `notes` TEXT DEFAULT NULL,
    FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`stock_id`) REFERENCES `stocks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Başlangıç Verileri
INSERT INTO `stocks` (`symbol`, `exchange`, `name`, `type`, `is_active`) VALUES
('AAPL', 'NASDAQ', 'Apple Inc.', 'stock', TRUE),
('MSFT', 'NASDAQ', 'Microsoft Corporation', 'stock', TRUE),
('bitcoin', 'CRYPTO', 'Bitcoin', 'crypto', TRUE),
('ethereum', 'CRYPTO', 'Ethereum', 'crypto', TRUE);

INSERT INTO `portfolios` (`name`, `initial_balance`, `current_balance`, `is_active`) VALUES
('Ana Cüzdan', 10000.00, 10000.00, TRUE);
