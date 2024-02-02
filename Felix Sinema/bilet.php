<?php
require 'auth.php';// Oturum kontrolü için 'auth.php' dosyasını dahil et
require 'header.php';// Sayfa başlığını ve menüyü içeren 'header.php' dosyasını dahil et


if (!isset($_GET['gosterim_id']) || empty($_GET['gosterim_id'])) {
    header('Location: index.php');// 'gosterim_id' parametresi belirtilmemiş veya boşsa, kullanıcıyı 'index.php' sayfasına yönlendir
}

$gosterim_id = $_GET['gosterim_id'];

// Gösterimi veritabanından sorgula
$sql = "SELECT 
so_gosterim.id as gosterim_id, 
so_salon.sinema_id,
so_sinema.ad as sinema_ad,
so_gosterim.salon_id, 
so_salon.ad as salon_ad, 
so_salon.kapasite,
so_gosterim.film_id, 
so_film.ad as film_ad,
so_film.dil as film_dil,
so_gosterim.seans_id,
so_seans.zaman,
so_gosterim.tarih,
(select so_bilet.id from so_bilet where so_bilet.gosterim_id = so_gosterim.id) as bilet_id,
(select so_bilet.ucret from so_bilet where so_bilet.gosterim_id = so_gosterim.id) as bilet_ucret,
IF((select so_bilet.id from so_bilet where so_bilet.gosterim_id = so_gosterim.id) IS NOT NULL, (select COUNT(*) from so_satis where so_satis.bilet_id = (select so_bilet.id from so_bilet where so_bilet.gosterim_id = so_gosterim.id)), 0) as satis_count
FROM so_gosterim 
INNER JOIN so_salon ON so_gosterim.salon_id = so_salon.id
INNER JOIN so_seans ON so_gosterim.seans_id = so_seans.id
INNER JOIN so_sinema ON so_salon.sinema_id = so_sinema.id
INNER JOIN so_film ON so_gosterim.film_id = so_film.id";

$where = ' WHERE so_gosterim.id=' . $gosterim_id;// 'gosterim_id' parametresine göre filtreleme yap

$qs = $db->query($sql . $where . ' ORDER BY so_film.id')->fetchAll(PDO::FETCH_ASSOC);// Sorguyu çalıştır ve sonuçları al

if (!$qs) {
    alert('Gösterim Bulunamadı', 'danger');// Gösterim bulunamazsa hata mesajı göster ve işlemi sonlandır
    die();
}

$q = $qs[0];// İlk gösterim sonucunu al

if (!$q['bilet_id']) {
    alert('Biletler Henüz Satışa Sunulmamıştır.', 'warning');// Bilet ID'si yoksa hata mesajı göster ve işlemi sonlandır
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['secilen_koltuklar']) && !empty($_POST['secilen_koltuklar'])) {
        $secilen_koltuklar = $_POST['secilen_koltuklar'];// Seçilen koltukları al
        $data = [];
        foreach ($secilen_koltuklar as $koltuk) {
            $data[] = [
                'bilet_id' => $q['bilet_id'],
                'koltuk_no' => $koltuk,
                'user_id' => $_SESSION['sinema_login']['id']
            ]; // Seçilen koltuklar için veri dizisi oluştur
        }
        $results = $db->insertAll(\Database\MySqlDb::TABLE_SATIS, $data);

        $hata = false;
        foreach ($results as $result) {
            if (!$result) {
                $hata = true;// Hata oluştuysa 'hata' değişkenini true yap
            }
        }
        if ($hata) {
            alert('Bilet Satın Alırken Hata Oluştu', 'danger');// Hata olduğunda hata mesajı göster

        }
        alert('Biletinizi Başarıyla Satın Aldınız.', 'success');// Bilet başarıyla satın alındığında başarı mesajı göster
    } else {
        alert("Lütfen Koltuk Seçiniz", 'danger');// Hiç koltuk seçilmediğinde hata mesajı göster
    }
}
if($q['bilet_id']){
    $satilan_koltuklar = $db->get(\Database\MySqlDb::TABLE_SATIS, 'bilet_id=' . $q['bilet_id']);// Veritabanından satılan koltukları al
    $satilan_koltuklar = array_column($satilan_koltuklar, 'koltuk_no');// Sadece koltuk numaralarını içeren bir dizi oluştur

    if (($q['kapasite'] - $q['satis_count']) == 0) {
        alert('Boş Koltuk Kalmadı', 'danger');// Boş koltuk kalmadığında hata mesajı göster
    }
}

?>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3><strong><?= $q['film_ad'] ?></strong></h3>
                                    <h4><?= $q['film_dil'] ?></h4>
                                    <h4><i class="fa fa-calendar"></i> <?= $q['tarih'] ?> <i
                                                class="fa fa-clock-o"></i> <?= $q['zaman'] ?></h4>
                                    <br/>
                                    <h4><i class="fa fa-map-marker"></i> <?= $q['sinema_ad'] ?> - <?= $q['salon_ad'] ?>
                                    </h4>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="pull-right"><strong><i class="fa fa-try"></i> <?= $q['bilet_ucret'] ?>
                                        </strong></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="well">
                            <div style="<?= ($q['kapasite'] - $q['satis_count']) == 0 ? 'pointer-events: none; opacity: 0.4;' : '' ?>">
                                <h5><i class="fa fa-bookmark-o"></i> <?= $q['kapasite'] - $q['satis_count'] ?> koltuk
                                    müsait
                                </h5>
                                <h4>Seçilen Koltuk</h4>
                                <form method="post">
                                    <h5 id="koltuklar">
                                        <input type="hidden" name="secilen_koltuklar"/>
                                    </h5>
                                    <button type="submit" class="btn btn-danger">SATIN AL</button>
									<a href="http://localhost/Felix%20Sinema/" class="btn btn-danger">VAZGEÇ</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 <?= !$q['bilet_id'] ? 'hidden' : ''; ?>">
                <div class="row"
                     style="margin-bottom: 10px; background-color: #fff;  border: 1px dashed #ce8483; border-radius: 10px;">
                    <div class="col-md-12">
                        <h5 class="text-center text-info">
                            <strong>PERDE</strong>
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <?= satirNumaralariDiz(); ?>
                        </div>
                        <?= koltuklariDiz(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
function satirNumaralariDiz()
{
    ?>
    <?php for ($k = 1; $k <= 10; $k++) { ?>
    <div class="col-xs-1 text-center <?= $k == 1 ? 'col-xs-offset-1' : '' ?>">
        <div class="btn"
             style="width: 40px; height: 35px;  background: #5cb85c; color: #fff; border-radius: 5px; cursor: default; margin-left:1px; margin-right:1px; margin-bottom: 8px; ">
            <?= $k; ?>
        </div>
    </div>
<?php } ?>
    <?php
}

function koltuklariDiz()
{
    global $q;
    global $satilan_koltuklar;
    ?>
    <?php for ($j = 0; $j < $q['kapasite'] / 10; $j++) { ?>
    <div class="row">
        <div class="col-xs-1 text-center">
            <div class="btn"
                 style="width: 35px; height: 40px;  background: #5cb85c; color: #fff; border-radius: 5px; cursor: default; margin-bottom:1px; margin-top:1px; margin-right: 10px; ">
                <?= chr(65 + $j) ?>
            </div>
        </div>
        <?php for ($i = 0; $i < 10; $i++) { ?>
            <div class="col-xs-1 text-center">
                <?php if (in_array($i + $j * 10 + 1, $satilan_koltuklar)) { ?>
                    <div class="btn btn-default koltuk-satilan"
                         style="width: 40px; height: 40px;  background: #ce8483; border-radius: 5px; cursor: no-drop; margin:1px; padding: 5px; pointer-events: none;"
                         koltuk="<?= $i + $j * 10 + 1 ?>">
                        <img src="img/seat_32.png" width="30px"
                             onmouseover="this.style.filter = 'invert(100%)'"
                             onmouseout="this.style.filter = ''"/>
                    </div>
                <?php } else { ?>
                    <div class="btn btn-default koltuk"
                         style="width: 40px; height: 40px;  background: #fff;  border-radius: 5px; cursor: pointer; margin:1px; padding: 5px"
                         koltuk="<?= $i + $j * 10 + 1 ?>">
                        <img src="img/seat_32.png" width="30px"
                             onmouseover="this.style.filter = 'invert(100%)'"
                             onmouseout="this.style.filter = ''"/>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
} ?>
    <?php
}
