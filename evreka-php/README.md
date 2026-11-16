# Evreka Raf — HTML + CSS + JavaScript + PHP + MySQL + Bootstrap

Kitap bağış, ödünç ve takas platformu (okul projesi).

## Kullanılan teknolojiler
| Teknoloji | Ne için kullanıldı |
|---|---|
| **HTML5** | Sayfa yapısı (`index.html`, `veritabani.php`) |
| **CSS3** | Ana sitenin tasarımı: tema (açık/koyu), mobil uyum, yüksek kontrast |
| **JavaScript** | Sitenin tüm etkileşimi: giriş, kitaplar, talepler, mesajlaşma, kulüpler, harita, bildirimler |
| **PHP 8** | Sunucu tarafı: `api/state.php` verileri okur/yazar, `db.php` veritabanı katmanı |
| **MySQL** (WampServer) | Verilerin saklandığı veritabanı (`evrekaraf`, 13 tablo) |
| **PDO** | PHP'nin güvenli veritabanı bağlantısı (hazır sorgular, transaction) |
| **Bootstrap 5** (CDN) | `veritabani.php` yönetim/inceleme sayfası arayüzü |
| **Google Fonts** | Yazı tipleri (Bricolage Grotesque, Figtree) |

## Hangi IDE?
**Visual Studio Code** önerilir (ücretsiz, hafif). Eklenti: *PHP Intelephense*. Apache WampServer'dan geldiği için ek bir sunucu eklentisi gerekmez.
Alternatif: PhpStorm (öğrenci lisansı ücretsiz). Visual Studio PHP için uygun değildir.

## Adım adım kurulum (WampServer)
1. **WampServer'ı indir ve kur:** https://www.wampserver.com (Windows için; gerekirse kurulumda istediği Visual C++ paketlerini de yükle).
2. WampServer'ı başlat. Sağ alttaki (saatin yanındaki) **W simgesi YEŞİL** olunca Apache ve MySQL hazırdır. Turuncu/kırmızı ise tam çalışmıyor demektir (aşağıdaki sorun giderme bölümüne bak).
3. Bu klasörü (`evreka-php`) **`C:\wamp64\www\`** içine kopyala → yol: `C:\wamp64\www\evreka-php\`
4. **VS Code**'u aç → *File → Open Folder* → `C:\wamp64\www\evreka-php` seç.
5. Tarayıcıda aç: **http://localhost/evreka-php/index.html**
   - İlk açılışta `evrekaraf` veritabanı ve 13 tablo **otomatik** oluşur, örnek verilerle dolar.
6. Veritabanını görmek için: **http://localhost/evreka-php/veritabani.php** (Bootstrap sayfası) veya **http://localhost/phpmyadmin** (kullanıcı `root`, şifre boş) → `evrekaraf`.

**Demo hesaplar:** yönetici `admin@evrekaraf.com` / `admin123` · üye `okur@evrekaraf.com` / `okur123`

### MySQL ayarları (WampServer)
WampServer varsayılanı: kullanıcı `root`, **şifre boş**, port **3306** (MySQL). Wamp'ta MariaDB de kuruluysa onun portu genelde **3307**'dir.
Hangisini kullandığını W simgesine tıklayıp *MySQL* ya da *MariaDB* menüsünden görebilirsin. Port veya şifre farklıysa `config.php` içindeki `port`, `user`, `pass` değerlerini düzenle.
Tabloları elle kurmak istersen phpMyAdmin → *İçe Aktar* ile `sql/evrekaraf_ornek_veri.sql` dosyasını yükle (tablolar + örnek veriler gelir). Yalnızca boş tablolar için `sql/schema_mysql.sql` kullanılabilir.

## Klasör yapısı
```
index.html         Site (HTML + CSS + JS)
api/state.php      REST API: GET verileri okur, PUT verileri MySQL'e yazar
db.php             PDO bağlantısı, tabloları oluşturma, okuma/yazma
config.php         Veritabanı ayarları
veritabani.php     Bootstrap ile tablo görüntüleme sayfası
sql/               schema_mysql.sql (WampServer) · schema_sqlite.sql (yalnızca test)
```

## Nasıl çalışır?
1. Sayfa açılır → JavaScript `api/state.php`'ye GET atar → PHP, MySQL tablolarından veriyi okur.
2. Kullanıcı bir işlem yapar (kitap ekle, talep, mesaj, kulüp…) → JavaScript yaklaşık 0,4 sn sonra PUT ile gönderir → PHP tek **transaction** içinde tablolara yazar.

## Sorun giderme
- **"Veritabanı hatası"**: MySQL çalışmıyor olabilir (W simgesi yeşil mi?), port yanlış olabilir (3306 / 3307) ya da `config.php` şifresi yanlış.
- **W simgesi turuncu / kırmızı**: Genellikle 80 numaralı port başka bir program tarafından kullanılıyordur (Skype, IIS, başka bir Apache). Programı kapat veya W simgesi → *Apache → Port'u kullan* menüsünden 8080 gibi bir port seç; adres `http://localhost:8080/evreka-php/` olur.
- **Sayfa açılıyor ama veri kalıcı değil**: `index.html`'i çift tıklayıp `file://` ile açtın; `http://localhost/evreka-php/index.html` adresini kullan.
- **404 hatası**: Klasör `C:\wamp64\www\` içinde değil ya da adı farklı.

## Bilinen sınırlar (raporunda yazabilirsin)
- Şifreler düz metin saklanıyor ve giriş kontrolü tarayıcıda yapılıyor. Geliştirme: `password_hash()` / `password_verify()` ve PHP oturumu (`$_SESSION`).
- Veri tek bir uç noktadan toplu yazılıyor; geliştirme: her işlem için ayrı uç nokta (`api/books.php` vb.).
- E-posta doğrulama kodları demo; gerçek e-posta gönderimi yok (PHPMailer ile eklenebilir).
- Harita şematik, ISBN verisi demo listeden geliyor.
