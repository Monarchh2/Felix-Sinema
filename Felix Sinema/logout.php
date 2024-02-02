<?php
@session_start(); // PHP oturumunu başlat

unset($_SESSION['sinema_login']); // 'sinema_login' oturum değişkenini sil

header('Location: index.php'); // Yönlendirme yap ve 'index.php' sayfasına git
die(); // İşlemi sonlandır
?>
