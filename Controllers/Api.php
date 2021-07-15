<?php 
$subscriber = 'Hesap Adı';
$password = 'Hesap Şifre';
$originator = 'Telefon Numarası';
date_default_timezone_set('Europe/Istanbul');
function time_ago($datetime){
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $time_arr = [
        'y' => 'Yıl',
        'm' => 'Ay',
        'w' => 'Hafta',
        'd' => 'Gün',
        'h' => 'Saat',
        'i' => 'Dakika',
        's' => 'Saniye'
    ];
    foreach($time_arr as $k => &$v){
        if($diff->$k){
            $v = $diff->$k . ' ' . $v;
        }else{
            unset($time_arr[$k]);
        }
    }
    return $time_arr ? implode(', ', array_slice($time_arr, 0, 1)) . ' Önce' : 'Hemen Şimdi';
}
if(isset($_POST['DataRefresh'])){
    $xmlFile = '<?xml version="1.0" encoding="utf-8"?><increport aboneno="'.$originator.'" pwd="'.$password.'"/>';
    $url = 'https://smsgw.mutlucell.com/smsgw-ws/gtincmngapi';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $xmlFile,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/xml'
        ),
    ));
    $respons = curl_exec($curl); curl_close($curl);
    $dataN = explode("\n", $respons);
    switch ($respons){
        case 20: echo "Error MutluCell: Post edilen xml eksik veya hatalı."; break;
        case 23: echo "Error MutluCell: Abone no ya da parolanız hatalı."; break;
        case 30: echo "Error MutluCell: Hesap Aktivasyonu sağlanmamış."; break;
        default: 
            $dataNCount = count($dataN);
            $sayiN = 0;
            $return = "";
            foreach($dataN as $N){
                $sayiN++;
                if($sayiN < $dataNCount){
                    $dataT = explode("\t", $N);
                    $tarih = explode(".", $dataT[0]);
                    $return .= '<tr>';
                    $return .= '<td><button class="btn btn-primary" data-phone="'.$dataT[1].'"><i class="fas fa-comment-dots"></i></button></td><td>'.$dataT[1].'<small class="d-block">'.$dataT[2].'</small></td><td>'.time_ago($tarih[0]).'</td>';
                    $return .= '</tr><tr class="spacer"><td colspan="100"></td></tr>';
                }
            }
            echo 'Success '.$return;
        ;
    }
}
if(isset($_POST['DataPost'])){
    $xmlFile = '<?xml version="1.0" encoding="utf-8"?><smspack ka="'.$subscriber.'" pwd="'.$password.'" org="'.$originator.'"><mesaj><metin>'.$_POST['smsValue'].'</metin><nums>'.$_POST['smsPhone'].'</nums></mesaj></smspack>';
    $url = 'https://smsgw.mutlucell.com/smsgw-ws/sndblkex';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $xmlFile,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/xml'
        ),
    ));
    $respons = curl_exec($curl); curl_close($curl);
    switch ($respons){
        case 20: echo "Error MutluCell: Post edilen xml eksik veya hatalı."; break;
        case 21: echo "Error MutluCell: Kullanılan originatöre sahip değilsiniz."; break;
        case 22: echo "Error MutluCell: Kontörünüz yetersiz."; break;
        case 23: echo "Error MutluCell: Abone no ya da parolanız hatalı."; break;
        case 24: echo "Error MutluCell: Şu anda size ait başka bir işlem aktif."; break;
        case 25: echo "Error MutluCell: SMSC Stopped (Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin)."; break;        
        case 30: echo "Error MutluCell: Hesap Aktivasyonu sağlanmamış."; break;
        default: 
            echo 'Success ';
        ;
    }
}
if(isset($_POST['CreditControl'])){
    $xmlFile = '<?xml version="1.0" encoding="utf-8"?><smskredi ka="'.$subscriber.'" pwd="'.$password.'"/>';
    $url = 'https://smsgw.mutlucell.com/smsgw-ws/gtcrdtex';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $xmlFile,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/xml'
        ),
    ));
    $respons = curl_exec($curl); curl_close($curl);
    switch ($respons){
        case 20: echo "Error MutluCell: Post edilen xml eksik veya hatalı."; break;
        case 23: echo "Error MutluCell: Abone no ya da parolanız hatalı."; break;
        default: 
            $credit = explode("$", $respons);
            echo 'Success '.$credit[1].' TL';
        ;
    }
}
?>