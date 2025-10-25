# Laravel IP Restriction

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://packagist.org/packages/digitalcorehub/laravel-ip-restriction)
[![Total Downloads](https://img.shields.io/packagist/dt/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://packagist.org/packages/digitalcorehub/laravel-ip-restriction)
[![Build Status](https://img.shields.io/travis/digitalcorehub/laravel-ip-restriction/master.svg?style=flat-square)](https://travis-ci.org/digitalcorehub/laravel-ip-restriction)
[![Quality Score](https://img.shields.io/scrutinizer/g/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://scrutinizer-ci.com/g/digitalcorehub/laravel-ip-restriction)
[![StyleCI](https://github.styleci.io/repos/123456789/shield)](https://github.styleci.io/repos/123456789)
[![License](https://img.shields.io/packagist/l/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://packagist.org/packages/digitalcorehub/laravel-ip-restriction)

**Türkçe:** Uygulamanıza IP adreslerine göre erişimi kısıtlamak için güçlü ve esnek bir Laravel paketi. Bu paket, CIDR notasyonu desteği, proxy header işleme, önbellekleme ve detaylı loglama dahil olmak üzere kapsamlı IP filtreleme yetenekleri sağlar.

**English:** A powerful and flexible Laravel package for restricting access to your application based on IP addresses. This package provides comprehensive IP filtering capabilities including CIDR notation support, proxy header handling, caching, and detailed logging.

## ✨ Özellikler / Features

- 🔒 **IP Adres Filtreleme / IP Address Filtering** - Belirli IP adreslerine veya IP aralıklarına erişimi kısıtla / Restrict access to specific IP addresses or IP ranges
- 🌐 **CIDR Notasyonu Desteği / CIDR Notation Support** - IP aralığı kısıtlamaları için CIDR notasyonu kullan (örn: `192.168.1.0/24`) / Use CIDR notation for IP range restrictions (e.g., `192.168.1.0/24`)
- 🔄 **Proxy Desteği / Proxy Support** - Çeşitli proxy header'larını işle (Cloudflare, load balancer, vb.) / Handle various proxy headers (Cloudflare, load balancers, etc.)
- ⚡ **Önbellekleme / Caching** - Gelişmiş performans için yerleşik önbellekleme / Built-in caching for improved performance
- 📊 **Loglama / Logging** - Yetkisiz erişim denemelerinin kapsamlı loglanması / Comprehensive logging of unauthorized access attempts
- 🛠️ **Artisan Komutları / Artisan Commands** - Komut satırı üzerinden IP kısıtlamalarını yönet / Manage IP restrictions via command line
- 🧪 **İyi Test Edilmiş / Well Tested** - Kapsamlı test kapsamı / Comprehensive test coverage
- 📱 **IPv4 & IPv6** - Hem IPv4 hem IPv6 adresleri için destek / Support for both IPv4 and IPv6 addresses
- ⚙️ **Yapılandırılabilir / Configurable** - Laravel config dosyaları aracılığıyla yüksek düzeyde yapılandırılabilir / Highly configurable through Laravel config files

## 📋 Gereksinimler / Requirements

- PHP 8.2 veya üzeri / PHP 8.2 or higher
- Laravel 10.0 veya üzeri / Laravel 10.0 or higher
- Composer

## 🚀 Kurulum / Installation

Paketi Composer ile kurabilirsiniz / You can install the package via Composer:

```bash
composer require digitalcorehub/laravel-ip-restriction
```

Paket otomatik olarak service provider ve facade'ını kaydedecektir / The package will automatically register its service provider and facade.

## ⚙️ Yapılandırma / Configuration

Yapılandırma dosyasını yayınlayın / Publish the configuration file:

```bash
php artisan vendor:publish --tag=ip-restriction-config
```

Bu, aşağıdaki seçeneklerle `config/ip-restriction.php` dosyasını oluşturacaktır / This will create `config/ip-restriction.php` with the following options:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Allowed IP Addresses
    |--------------------------------------------------------------------------
    |
    | This array contains the IP addresses that are allowed to access your
    | application when using the IP restriction middleware.
    |
    */
    'allowed_ips' => [
        '127.0.0.1',        // Localhost
        '::1',              // IPv6 localhost
        '192.168.1.0/24',   // Local network range
        '10.0.0.0/8',       // Private network range
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how long the allowed IPs list should be cached.
    | Set to null to disable caching.
    |
    */
    'cache_ttl' => 3600, // 1 hour in seconds

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether to log unauthorized access attempts.
    |
    */
    'log_attempts' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Error Message
    |--------------------------------------------------------------------------
    |
    | Customize the error message shown when access is denied.
    |
    */
    'error_message' => 'Access denied. Your IP address is not authorized.',
];
```

## 🎯 Kullanım / Usage

### Temel Middleware Kullanımı / Basic Middleware Usage

Middleware'i route'larınıza uygulayın / Apply the middleware to your routes:

```php
// Bireysel route'ları koru / Protect individual routes
Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('restrict_to_ip');

// Route gruplarını koru / Protect route groups
Route::middleware(['restrict_to_ip'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::get('/admin/settings', [AdminController::class, 'settings']);
});

// Controller'ları koru / Protect controllers
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('restrict_to_ip');
    }
}
```

### Facade Kullanımı / Using the Facade

Paketi programatik olarak da kullanabilirsiniz / You can also use the package programmatically:

```php
use DigitalCoreHub\LaravelIpRestriction\Facades\LaravelIpRestriction;

// IP'nin izin verilip verilmediğini kontrol et / Check if an IP is allowed
if (LaravelIpRestriction::isIpAllowed('192.168.1.100')) {
    // IP'ye izin verildi / IP is allowed
}

// İstemci IP'sini al / Get client IP
$clientIp = LaravelIpRestriction::getClientIp();

// İzin verilen listeye IP ekle (sadece çalışma zamanında) / Add IP to allowed list (runtime only)
LaravelIpRestriction::addAllowedIp('203.0.113.1');

// İzin verilen listeden IP çıkar (sadece çalışma zamanında) / Remove IP from allowed list (runtime only)
LaravelIpRestriction::removeAllowedIp('203.0.113.1');

// Cache'i temizle / Clear cache
LaravelIpRestriction::clearCache();
```

### IP Adres Formatları / IP Address Formats

Paket çeşitli IP adres formatlarını destekler / The package supports various IP address formats:

```php
'allowed_ips' => [
    '127.0.0.1',           // Tek IPv4 adresi / Single IPv4 address
    '::1',                 // Tek IPv6 adresi / Single IPv6 address
    '192.168.1.0/24',      // IPv4 CIDR aralığı / IPv4 CIDR range
    '2001:db8::/32',       // IPv6 CIDR aralığı / IPv6 CIDR range
    '10.0.0.0/8',          // Büyük IPv4 aralığı / Large IPv4 range
    '172.16.0.0/12',       // Özel IPv4 aralığı / Private IPv4 range
],
```

## 🛠️ Artisan Komutları / Artisan Commands

Paket yararlı Artisan komutları içerir / The package includes helpful Artisan commands:

```bash
# Tüm izin verilen IP'leri listele / List all allowed IPs
php artisan ip-restriction:list

# IP kısıtlama cache'ini temizle / Clear the IP restriction cache
php artisan ip-restriction:list --clear-cache
```

## 🔧 Gelişmiş Yapılandırma / Advanced Configuration

### Özel Hata Mesajları / Custom Error Messages

Erişim reddedildiğinde gösterilen hata mesajını özelleştirebilirsiniz / You can customize the error message shown when access is denied:

```php
// config/ip-restriction.php dosyasında / In config/ip-restriction.php
'error_message' => 'Üzgünüz, konumunuzdan erişime izin verilmiyor. / Sorry, access from your location is not permitted.',
```

### Loglamayı Devre Dışı Bırakma / Disable Logging

Yetkisiz erişim denemelerinin loglanmasını devre dışı bırakmak için / To disable logging of unauthorized access attempts:

```php
// config/ip-restriction.php dosyasında / In config/ip-restriction.php
'log_attempts' => false,
```

### Önbellekleme Devre Dışı Bırakma / Disable Caching

İzin verilen IP listesinin önbelleklemesini devre dışı bırakmak için / To disable caching of the allowed IPs list:

```php
// config/ip-restriction.php dosyasında / In config/ip-restriction.php
'cache_ttl' => null,
```

### Proxy Yapılandırması / Proxy Configuration

Paket çeşitli proxy header'larını otomatik olarak işler / The package automatically handles various proxy headers:

- `HTTP_CF_CONNECTING_IP` (Cloudflare)
- `HTTP_X_FORWARDED_FOR` (Load balancer/proxy)
- `HTTP_X_FORWARDED` (Proxy)
- `HTTP_X_CLUSTER_CLIENT_IP` (Cluster)
- `HTTP_FORWARDED_FOR` (Proxy)
- `HTTP_FORWARDED` (Proxy)
- `HTTP_CLIENT_IP` (Proxy)
- `REMOTE_ADDR` (Standart / Standard)

## 🧪 Test Etme / Testing

Testleri çalıştırmak için / Run the tests with:

```bash
composer test
```

Veya kapsam ile çalıştırın / Or run with coverage:

```bash
composer test-coverage
```

## 📊 Loglama / Logging

Loglama etkinleştirildiğinde, yetkisiz erişim denemeleri aşağıdaki bilgilerle loglanır / When logging is enabled, unauthorized access attempts are logged with the following information:

```json
{
    "level": "warning",
    "message": "Unauthorized IP access attempt",
    "context": {
        "ip": "203.0.113.1",
        "user_agent": "Mozilla/5.0...",
        "url": "https://example.com/admin",
        "method": "GET",
        "timestamp": "2024-01-15T10:30:00.000000Z"
    }
}
```

## 🔒 Güvenlik Değerlendirmeleri / Security Considerations

- **Her zaman HTTPS kullanın** / **Always use HTTPS** - Üretimde IP sahteciliğini önlemek için / in production to prevent IP spoofing
- **Proxy'nizi doğru yapılandırın** / **Configure your proxy correctly** - Gerçek IP adreslerinin geçirildiğinden emin olmak için / to ensure real IP addresses are passed
- **Logları düzenli olarak inceleyin** / **Regularly review logs** - Şüpheli erişim denemeleri için / for suspicious access attempts
- **Mümkün olduğunda geniş aralıklar yerine belirli IP aralıkları kullanın** / **Use specific IP ranges** rather than broad ranges when possible
- **IP kısıtlamalarının yanında ek kimlik doğrulama kullanmayı düşünün** / **Consider using additional authentication** alongside IP restrictions

## 🚀 Performans / Performance

- **Önbellekleme / Caching**: IP listeleri gelişmiş performans için önbelleğe alınır / IP lists are cached for improved performance
- **Verimli eşleştirme / Efficient matching**: Optimize edilmiş IP eşleştirme algoritmaları / Optimized IP matching algorithms
- **Minimal yük / Minimal overhead**: Minimal performans etkisi olan hafif middleware / Lightweight middleware with minimal performance impact

## 🤝 Katkıda Bulunma / Contributing

Katkılarınızı memnuniyetle karşılıyoruz! / We welcome contributions! Detaylar için [Katkıda Bulunma Rehberi](CONTRIBUTING.md) / [Contributing Guide](CONTRIBUTING.md) sayfamıza bakın.

1. Repository'yi fork edin / Fork the repository
2. Özellik dalınızı oluşturun / Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin / Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Dalı push edin / Push to the branch (`git push origin feature/amazing-feature`)
5. Pull Request açın / Open a Pull Request

## 📝 Değişiklik Günlüğü / Changelog

Son değişiklikler hakkında daha fazla bilgi için [CHANGELOG](CHANGELOG.md) sayfamıza bakın / Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🐛 Hata Raporları / Bug Reports

Bir hata keşfederseniz, lütfen aşağıdaki bilgilerle bir issue açın / If you discover a bug, please open an issue with:
- Problemin net açıklaması / A clear description of the problem
- Sorunu yeniden üretme adımları / Steps to reproduce the issue
- Beklenen vs gerçek davranış / Expected vs actual behavior
- Ortam detaylarınız / Your environment details

## 🔐 Güvenlik Açıkları / Security Vulnerabilities

Bir güvenlik açığı keşfederseniz, lütfen issue tracker kullanmak yerine [security@digitalcorehub.com](mailto:security@digitalcorehub.com) adresine e-posta gönderin / If you discover a security vulnerability, please send an email to [security@digitalcorehub.com](mailto:security@digitalcorehub.com) instead of using the issue tracker.

## 📄 Lisans / License

MIT Lisansı (MIT) / The MIT License (MIT). Daha fazla bilgi için [Lisans Dosyası](LICENSE.md) / [License File](LICENSE.md) sayfamıza bakın.

## 🙏 Teşekkürler / Credits

- [DigitalCoreHub](https://github.com/DigitalCoreHub)
- [Tüm Katkıda Bulunanlar](../../contributors) / [All Contributors](../../contributors)

## 📞 Destek / Support

- 📧 E-posta / Email: [support@digitalcorehub.com](mailto:support@digitalcorehub.com)
- 🐛 Sorunlar / Issues: [GitHub Issues](https://github.com/digitalcorehub/laravel-ip-restriction/issues)
- 📖 Dokümantasyon / Documentation: [GitHub Wiki](https://github.com/digitalcorehub/laravel-ip-restriction/wiki)

---

[DigitalCoreHub](https://digitalcorehub.com) tarafından ❤️ ile yapıldı / Made with ❤️ by [DigitalCoreHub](https://digitalcorehub.com)
