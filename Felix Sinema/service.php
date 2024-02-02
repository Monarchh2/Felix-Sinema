<?php
require 'auth.php'; // 'auth.php' dosyasını dahil et
require 'loader.php'; // 'loader.php' dosyasını dahil et

if (isset($_GET['op'])) { // 'op' parametresi tanımlıysa
    switch ($_GET['op']) {
        case 'sinemalar': // 'op' değeri 'sinemalar' ise

            $sinemalar = $db->get(\Database\MySqlDb::TABLE_SINEMA); // 'sinema' tablosundaki tüm kayıtları al
            $res = '';
            foreach ($sinemalar as $sinema) { // Her sinema kaydı için
                $res .= '<option value="' . $sinema['id'] . '">' . $sinema['ad'] . '</option>'; // Seçenek HTML kodunu oluştur
            }

            json([
                'html' => $res // Oluşturulan HTML kodunu JSON formatında dönüştürerek gönder
            ]);

            break;

        case 'filmler': // 'op' değeri 'filmler' ise

            $filmler = $db->get(\Database\MySqlDb::TABLE_FILM); // 'film' tablosundaki tüm kayıtları al
            $res = '';
            foreach ($filmler as $film) { // Her film kaydı için
                $res .= '<option value="' . $film['id'] . '">' . $film['ad'] . '</option>'; // Seçenek HTML kodunu oluştur
            }

            json([
                'html' => $res // Oluşturulan HTML kodunu JSON formatında dönüştürerek gönder
            ]);

            break;

        case 'tarihler': // 'op' değeri 'tarihler' ise

            $res = '';
            for ($i = 1; $i <= 7; $i++) { // 1 ila 7 arasındaki günler için
                $date = date("Y-m-d", strtotime("+$i days")); // Gelecek günlerin tarihlerini al
                $res .= '<option value="' . $date . '">' . $date . '</option>'; // Seçenek HTML kodunu oluştur
            }

            json([
                'html' => $res // Oluşturulan HTML kodunu JSON formatında dönüştürerek gönder
            ]);

            break;
    }
}
?>

