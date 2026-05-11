# Aydın Oğulları İnşaat - İnsan Kaynakları Yönetim Sistemi

Modern ve profesyonel PHP tabanlı İK yönetim sistemi.

## 🎯 Özellikler

- ✅ Kullanıcı yönetimi ve rol tabanlı erişim kontrol
- ✅ Keşif (Teklif) modülü
- ✅ Müşteri ve ürün yönetimi
- ✅ Sipariş ve satın alma takibi
- ✅ Raporlama sistemi
- ✅ Veritabanı loglama (Monolog + MySQL)
- ✅ PDF ve Excel export
- ✅ Mail sistem entegrasyonu

## 🛠️ Teknolojiler

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: Bootstrap 4/5, jQuery, DataTables
- **Logging**: Monolog 3.9
- **Email**: PHPMailer
- **PDF**: DOMPDF
- **Excel**: PhpSpreadsheet

## 📋 Gereksinimler

- PHP 8.0+
- MySQL 5.7+
- Composer
- XAMPP (Development)

## 🚀 Kurulum

### 1. Projeyi İndir
```bash
git clone https://github.com/yourusername/aydinogullari-hris.git
cd aydinogullari-hris
```

### 2. Composer Bağımlılıklarını Kur
```bash
composer install
```

### 3. Veritabanını Oluştur
```bash
mysql -u root -e "CREATE DATABASE aydinogu_aydinogullari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 4. SQL Dump'ı İçeri Aktar
```bash
mysql -u root aydinogu_aydinogullari < backup_database.sql
```

### 5. XAMPP'de Çalıştır
- Apache ve MySQL'i başlat
- `http://localhost/aydinogullariysc/` adresine git

### 6. Admin Girişi
- Kullanıcı adı: admin
- Şifre: (config.php'de kontrol et)

## 📁 Proje Yapısı

```
├── App/
│   ├── Handler/          # Custom handlers
│   ├── Helper/           # Helper sınıfları
│   ├── Logging/          # Logger factory
│   ├── Model/            # Database models
│   └── api/              # REST API endpoints
├── configs/              # Konfigürasyon dosyaları
├── include/              # Header, footer, sidebar
├── logs/                 # Application logs
├── migrations/           # SQL migration dosyaları
├── pages/                # Sayfa modülleri
├── vendor/               # Composer bağımlılıkları
└── index.php             # Ana entry point
```

## 📝 Modüller

### Keşif (Kesif)
- Yeni keşif ekleme/düzenleme/silme
- Durum takibi (Bekliyor, İptal, Teklif Gönderildi)
- Veritabanı loglama
- Günlük dosya loglama

### Müşteri
- Müşteri listeleme
- Müşteri detayları
- İletişim bilgileri

### Raporlar
- Dinamik raporlama
- Excel export
- PDF export

## 🔐 Güvenlik

- PDO prepared statements (SQL injection koruması)
- Rol tabanlı erişim kontrol (RBAC)
- Şifre hash'leme
- Session management

## 📊 Loglama

Tüm işlemler veritabanında kaydediliyor:

```sql
SELECT * FROM logs WHERE module = 'kesif' ORDER BY created_at DESC;
```

## 🐛 Debugging

Development modunda hataları görmek için `index.php`'de:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📞 İletişim

**Proje Sahibi**: Aydın Oğulları İnşaat
**Email**: info@aydinogullari.com

## 📄 Lisans

Proprietary - Tüm hakları saklıdır.

---

**Son Güncelleme**: 11 Kasım 2025
