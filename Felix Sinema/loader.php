<?php
@session_start(); // PHP oturumunu başlat

spl_autoload_register(function ($class) {
    if (file_exists('class/' . str_replace('\\', '/', $class) . '.class.php'))
        include 'class/' . str_replace('\\', '/', $class) . '.class.php';
});

require 'func.php'; // 'func.php' dosyasını dahil et

$db = \Database\MySqlDb::getInstance(); // 'MySqlDb' sınıfından bir örnek oluştur
$db->setup('localhost', 'sinema', 'root', ''); // Veritabanı bağlantı ayarlarını yap (sunucu, veritabanı adı, kullanıcı adı, parola)
$db->connect(); // Veritabanına bağlan
$db->createAllTables(); // Gerekli tabloları oluştur (varsa zaten oluşturulmuş tabloları değiştirmez)



