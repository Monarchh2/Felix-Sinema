<?php

function cURL($url, $post = null, $requestHeader = null, $cookie = 'cookie.txt')
{
    // cURL ile HTTP isteği gönderen bir işlev
    // Parametreler: $url (URL), $post (POST verileri), $requestHeader (isteğe özel başlıklar), $cookie (çerez dosyası yolu)

    $ch = curl_init(); // cURL özkaynak oluşturma

    // cURL seçeneklerinin ayarlanması
    curl_setopt($ch, CURLOPT_URL, $url); // İstek gönderilecek URL
    curl_setopt($ch, CURLOPT_COOKIESESSION, 1); // Çerez oturumu
    curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($cookie)); // Çerezleri kaydetmek için kullanılan dosya
    curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie)); // Çerezleri yükleme için kullanılan dosya
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Sonucun bir değişkende döndürülmesi
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Yönlendirmelerin otomatik olarak takip edilmesi
    curl_setopt($ch, CURLOPT_HEADER, 1); // Yanıtın başlık kısmının da döndürülmesi
    curl_setopt($ch, CURLOPT_VERBOSE, 1); // Ayrıntılı hata ayıklama modu

    // İsteğe özel başlıkların ayarlanması
    if ($requestHeader)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);

    // POST verilerinin ayarlanması
    if ($post) {
        if (!is_array($post)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        } else {
            $postvars = http_build_query($post);
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        }
    }

    $result = curl_exec($ch); // cURL isteğini gerçekleştirme
    //$result = iconv('ISO-8859-9','UTF-8',$result);

    // İstek başlığını ve gövdesini ayırma
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($result, 0, $header_size);
    $body = substr($result, $header_size);

    curl_close($ch); // cURL özkaynağını kapatma

    // Başlık ve gövdeyi bir dizi olarak döndürme
    $arr = array(
        'header_size' => $header_size,
        'header' => $header,
        'body' => $body
    );
    return $arr;
}

function getMilliseconds()
{
    // Geçerli zamanı milisaniye cinsinden döndüren bir işlev
    return round(microtime(true) * 1000);
}

function println($data)
{
    // Veriyi ekrana yazdıran bir işlev
    // Eğer veri dizi ise düzgün bir şekilde formatlanır ve yazdırılır
    // Aksi halde veri doğrudan yazdırılır
    if (is_array($data)) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    } else {
        echo $data . '<br>';
    }
}

function parse_query($query_string)
{
    // Sorgu dizesini ayrıştıran bir işlev
    // Sorgu dizesi parametresi "&" karakterine göre bölünür
    // Her çift anahtar-değer çifti ayrıştırılır ve bir diziye eklenir
    $queries = array();
    $query_pairs = explode('&', trim($query_string));
    foreach ($query_pairs as $pair) {
        $pair = explode('=', $pair);
        $queries[$pair[0]] = $pair[1];
    }
    return $queries;
}

function only_number($string)
{
    // Bir dizedeki sadece sayıları içeren kısmı döndüren bir işlev
    // Dizedeki harfler, özel karakterler ve boşluklar kaldırılır
    return preg_replace("/[^0-9,.]/", "", $string);
}

function getBase64Image($img_src)
{
    // Verilen bir resim URL'sinden base64 kodunu döndüren bir işlev

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $img_src);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
    $res = curl_exec($ch);
    $rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    //$type 		= 	pathinfo($img_src, PATHINFO_EXTENSION);
    $base64 = base64_encode($res);

    return $base64;
}

function mysql_unreal_escape_string($string)
{
    // MySQL'de geçerli değilmiş gibi görünen karakterleri düzgün bir şekilde kaçırırken kullanılan bir işlev

    $characters = array('x00', 'n', 'r', '\\', '\'', '"', 'x1a');
    $o_chars = array("\x00", "\n", "\r", "\\", "'", "\"", "\x1a");
    for ($i = 0; $i < strlen($string); $i++) {
        if (substr($string, $i, 1) == '\\') {
            foreach ($characters as $index => $char) {
                if ($i <= strlen($string) - strlen($char) && substr($string, $i + 1, strlen($char)) == $char) {
                    $string = substr_replace($string, $o_chars[$index], $i, strlen($char) + 1);
                    break;
                }
            }
        }
    }
    return $string;
}

function array_to_xml($data, &$xml_data)
{
    // Bir dizi verisini XML'e dönüştüren bir işlev

    foreach ($data as $key => $value) {
        if (is_numeric($key)) {
            $key = 'item' . $key; // <0/>..<n/> sorunlarıyla uğraşmak için
        }
        if (is_array($value)) {
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key", htmlspecialchars("$value"));
        }
    }
}

function getDir()
{
    // Sunucu tarafındaki dizini döndüren bir işlev
    // Örneğin, "example.com/subfolder" gibi bir URL'de "example.com" döndürülür
    $url = $_SERVER['REQUEST_URI']; // Geçerli URL'yi döndürür
    $parts = explode('/', $url);
    $dir = $_SERVER['SERVER_NAME'];
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $dir .= $parts[$i] . "/";
    }
    return $dir;
}

function formatSizeUnits($bytes)
{
    // Baytları uygun bir birime dönüştüren bir işlev
    // Örneğin, 2048 bayt "2 KB" olarak döndürülür
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}


function alert($alertMsg, $alertType = 'info')
{
    // Bir uyarı mesajı görüntüleyen bir işlev
    echo '<div class="alert alert-dismissable alert-' . $alertType . '">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            ' . $alertMsg . '
           </div>';
}

function json(array $array)
{
    // Bir diziyi JSON formatında döndüren bir işlev

    $json = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    $result = '[]';
    if ($json && json_last_error() == JSON_ERROR_NONE) {
        $result = $json;
    } else {
        $result = '{"HATA": "JSON HATASI OLUŞTU. MESAJ: ' . json_last_error_msg() . '"}';
    }

    header('Content-type: application/json; charset=utf8');
    echo $result;
}

function array_sort($array, $on, $order = SORT_ASC)
{
    // Bir diziyi belirli bir anahtara göre sıralayan bir işlev

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

?>
