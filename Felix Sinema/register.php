
<?php
require 'header.php'; // 'header.php' dosyasını dahil et

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // İstek POST metoduyla yapıldıysa
    $post = $_POST; // $_POST dizisini $post değişkenine ata

    if (
        isset($post['ad']) && !empty($post['ad']) &&
        isset($post['kadi']) && !empty($post['kadi']) &&
        isset($post['parola']) && !empty($post['parola']) &&
        isset($post['parola_tekrar']) && !empty($post['parola_tekrar'])
    ) { // Gerekli alanlar (ad, kadi, parola, parola_tekrar) tanımlı ve boş değilse

        if ($post['parola'] != $post['parola_tekrar']) { // Parolalar eşleşmiyorsa
            alert('Parolalar eşleşmiyor', 'danger'); // Uyarı mesajı göster (danger stilinde)
        } else {
            unset($post['parola_tekrar']); // Parola_tekrar alanını kaldır
            $post['parola'] = md5($post['parola']); // Parola alanının değerini md5 ile şifrele

            if (!$db->get(\Database\MySqlDb::TABLE_KULLANICI, "kadi='" . $post['kadi'] . "'")) { // Kullanıcı adı veritabanında mevcut değilse
                $insert = $db->insert(\Database\MySqlDb::TABLE_KULLANICI, $post); // Kullanıcıyı veritabanına ekle

                if ($insert) { // Ekleme işlemi başarılıysa
                    alert('Başarıyla kayıt oldunuz :)', 'success'); // Başarılı kayıt mesajı göster (success stilinde)
                } else {
                    alert('Hata oluştu', 'danger'); // Hata mesajı göster (danger stilinde)
                }
            } else {
                alert('Kullanıcı adı sistemde zaten kayıtlı', 'warning'); // Uyarı mesajı göster (warning stilinde)
            }
        }
    }
}
?>



<div class="container">
    <form method="post">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default panel-collapse">
                    <div class="panel-heading">
                        Kullanıcı Kayıt
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Ad Soyad:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"
                                                                           aria-hidden="true"></i></span>
                                        <input type="text" required="required" class="form-control"
                                               placeholder="Ad-Soyad Giriniz" name="ad"
                                               autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">E-mail:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"
                                                                           aria-hidden="true"></i></span>
                                        <input type="email" class="form-control"
                                               placeholder="E-mail Adresi Giriniz" name="email"
                                               autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Cep Telefonu:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"
                                                                           aria-hidden="true"></i></span>
                                        <input type="tel" class="form-control"
                                               placeholder="Cep Telefonu Giriniz" name="tel"
                                               autocomplete="off"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Kullanıcı Adı:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"
                                                                           aria-hidden="true"></i></span>
                                        <input type="text" required="required" class="form-control"
                                               placeholder="Kullanıcı Adını Giriniz" name="kadi"
                                               autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Parola:</label>
                                    <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-unlock"
                                                                       aria-hidden="true"></i></span>
                                        <input type="password" class="form-control"
                                               placeholder="Parola Giriniz"
                                               name="parola" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Parola Tekrar:</label>
                                    <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-unlock"
                                                                       aria-hidden="true"></i></span>
                                        <input type="password" class="form-control"
                                               placeholder="Parolayı Tekrar Giriniz"
                                               name="parola_tekrar" autocomplete="off"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-success pull-right" type="submit">Kayıt Ol</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
