# Borsa Analiz ve Sinyal Sistemi Kurulum Rehberi

Bu rehber, projeyi bir web sunucusuna (Plesk, cPanel vb.) kurmak için gerekli adımları içerir.

## Gereksinimler

*   PHP 7.4 veya üstü
*   MySQL veya MariaDB veritabanı
*   Sunucuda `curl` eklentisinin aktif olması
*   Cron job (zamanlanmış görev) oluşturma yetkisi

---

### Adım 1: Dosyaların Sunucuya Yüklenmesi

1.  Bu projedeki `borsa-botu` klasörünün içindeki tüm dosyaları (`index.php`, `config.php`, `app/`, `views/` vb.) sunucunuzdaki ana web dizininize (`httpdocs`, `public_html` vb.) yükleyin.

---

### Adım 2: Veritabanının Oluşturulması ve Ayarlanması

1.  **Veritabanı Oluşturma:**
    *   Sunucu yönetim panelinizden (Plesk, cPanel) yeni bir veritabanı oluşturun. Örneğin, `borsa_db`.
    *   Bu veritabanı için bir kullanıcı oluşturun (örn: `borsa_user`) ve bu kullanıcıya bir şifre atayın.
    *   Oluşturduğunuz kullanıcıya, oluşturduğunuz veritabanı üzerinde tüm yetkileri (`ALL PRIVILEGES`) verin.

2.  **SQL Şemasını İçe Aktarma:**
    *   Yönetim panelinizden `phpMyAdmin`'e gidin.
    *   Sol menüden oluşturduğunuz veritabanını (`borsa_db`) seçin.
    *   Üst menüden **"İçe Aktar" (Import)** sekmesine tıklayın.
    *   "Dosya Seç" butonuna tıklayarak projenin içindeki `schema.sql` dosyasını seçin.
    *   Sayfanın altındaki "İçe Aktar" (Go/Import) butonuna tıklayın. Bu işlem, gerekli tüm tabloları ve başlangıç verilerini oluşturacaktır.

---

### Adım 3: Yapılandırma Dosyasının Düzenlenmesi

1.  Sunucunuza yüklediğiniz `config.php` dosyasını bir metin düzenleyici ile açın.

2.  Aşağıdaki veritabanı ayarlarını, **Adım 2**'de oluşturduğunuz bilgilerle güncelleyin:
    ```php
    define('DB_HOST', 'localhost'); // Genellikle bu şekilde kalır
    define('DB_NAME', 'borsa_db');     // Oluşturduğunuz veritabanı adı
    define('DB_USER', 'borsa_user');  // Oluşturduğunuz kullanıcı adı
    define('DB_PASS', 'YourStrongPassword'); // Oluşturduğunuz şifre
    ```

3.  API anahtarlarınızı `API_KEYS` dizisi içine girin.
    ```php
    define('API_KEYS', [
        'FINNHUB'       => 'SİZİN_FINNHUB_API_ANAHTARINIZ',
        'COINGECKO'     => 'SİZİN_COINGECKO_API_ANAHTARINIZ', // Gerekliyse
        'AI_API'        => 'SİZİN_AI_API_ANAHTARINIZ',
        // Diğerleri...
    ]);
    ```

4.  Eğer Telegram bildirimi kullanacaksanız, ilgili token ve chat ID'yi girin.

---

### Adım 4: Cron Job'un (Zamanlanmış Görev) Ayarlanması

Bu adım, sistemin otomatik olarak analiz yapıp sinyal üretmesi için **kritiktir**.

1.  Sunucu yönetim panelinizden **"Cron Jobs"** veya **"Zamanlanmış Görevler"** bölümüne gidin.

2.  Yeni bir görev ekleyin ve aşağıdaki ayarları yapın:
    *   **Görev Tipi (Task Type):** `PHP betiği çalıştır` (Run a PHP script) seçeneğini seçin.
    *   **Betik Yolu (Script path):** Sunucunuza yüklediğiniz `cron.php` dosyasının tam yolunu belirtin. Örneğin:
        `/var/www/vhosts/alanadiniz.com/httpdocs/cron.php`
        *Not: Bu yolu, paneldeki dosya yöneticisinden veya `pwd` komutuyla SSH üzerinden öğrenebilirsiniz.*
    *   **Çalıştırma (Run):** Görevin ne sıklıkla çalışacağını seçin. Analizlerin sıklığına göre `Saatte bir` (Hourly) veya `Günde bir` (Daily) gibi bir seçenek uygun olabilir. Örnek olarak saatte bir çalıştırmak için:
        *   Dakika: `0`
        *   Saat: `*`
        *   Ayın Günü: `*`
        *   Ay: `*`
        *   Haftanın Günü: `*`

3.  Görevi kaydedin ve aktif hale getirin.

---

### Kurulum Tamamlandı!

Artık web sitenize tarayıcınızdan erişebilir (`http://www.alanadiniz.com`), cron job'un çalışmasını bekleyerek üretilen sinyalleri "Sinyal Paneli"nde görebilirsiniz. İlk çalıştırmada veri çekme ve analiz biraz zaman alabilir. Cron job'un çıktısını (logları) panel üzerinden kontrol ederek herhangi bir hata olup olmadığını görebilirsiniz.
