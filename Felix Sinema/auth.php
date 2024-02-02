<?php
@session_start(); // Oturumu başlat

if (isset($_SESSION['sinema_login']) && !empty($_SESSION['sinema_login'])) {
    // Eğer 'sinema_login' adında bir oturum değişkeni tanımlanmış ve boş değilse
    // (yani kullanıcı oturum açmışsa), buradaki kod bloğunu çalıştır

} else {
    // Aksi takdirde (kullanıcı oturum açmamışsa)
    header('Location: login.php'); // Kullanıcıyı 'login.php' sayfasına yönlendir
    die(); // İşlemi sonlandır (bu noktaya kadar olan kodun çalışmasını durdur)
}    
