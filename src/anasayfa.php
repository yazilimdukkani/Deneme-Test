<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
  <?php

    //burasi insert


    


    
    

## 1. ADIM için örnek kodlar ##

    ####################### DÜZENLEMESİ ZORUNLU ALANLAR #######################
    #
    ## API Entegrasyon Bilgileri - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
    $merchant_id    = '1111';
    $merchant_key   = 'asdasdsd12gh';
    $merchant_salt  = 'asdvfgb678';
    #
    ## Müşterinizin sitenizde kayıtlı veya form vasıtasıyla aldığınız eposta adresi
    $email = $degisken->email;
    #
    ## Tahsil edilecek tutar.
    $payment_amount =$degisken->sum('fiyat')*100; //9.99 için 9.99 * 100 = 999 gönderilmelidir.
    #
    ## Sipariş numarası: Her işlemde benzersiz olmalıdır!! Bu bilgi bildirim sayfanıza yapılacak bildirimde geri gönderilir.
    $merchant_oid = $degisken->siparisno;
    #
    ## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız ad ve soyad bilgisi
    $user_name = $degisken->user;
    #
    ## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız adres bilgisi
    $user_address =$degisken->adres;
    #
    ## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız telefon bilgisi
    $user_phone = $degisken->telefon;
    #
    ## Başarılı ödeme sonrası müşterinizin yönlendirileceği sayfa
    ## !!! Bu sayfa siparişi onaylayacağınız sayfa değildir! Yalnızca müşterinizi bilgilendireceğiniz sayfadır!
    ## !!! Siparişi onaylayacağız sayfa "Bildirim URL" sayfasıdır (Bakınız: 2.ADIM Klasörü).
    $merchant_ok_url = "http://localhost/turizm/public/olumlupos";
    #
    ## Ödeme sürecinde beklenmedik bir hata oluşması durumunda müşterinizin yönlendirileceği sayfa
    ## !!! Bu sayfa siparişi iptal edeceğiniz sayfa değildir! Yalnızca müşterinizi bilgilendireceğiniz sayfadır!
    ## !!! Siparişi iptal edeceğiniz sayfa "Bildirim URL" sayfasıdır (Bakınız: 2.ADIM Klasörü).
    $merchant_fail_url = "http://localhost/turizm/public/olumsuzkart";
    #
    ## Müşterinin sepet/sipariş içeriği
    $user_basket = $degisken->urun;
    #
    /* ÖRNEK $user_basket oluşturma - Ürün adedine göre array'leri çoğaltabilirsiniz
    $user_basket = base64_encode(json_encode(array(
        array("Örnek ürün 1", "18.00", 1), // 1. ürün (Ürün Ad - Birim Fiyat - Adet )
        array("Örnek ürün 2", "33.25", 2), // 2. ürün (Ürün Ad - Birim Fiyat - Adet )
        array("Örnek ürün 3", "45.42", 1)  // 3. ürün (Ürün Ad - Birim Fiyat - Adet )
    )));
    */
    ############################################################################################

    ## Kullanıcının IP adresi


    if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }

    ## !!! Eğer bu örnek kodu sunucuda değil local makinanızda çalıştırıyorsanız
    ## buraya dış ip adresinizi (https://www.whatismyip.com/) yazmalısınız. Aksi halde geçersiz paytr_token hatası alırsınız.
    $user_ip=$ip;
    ##

    ## İşlem zaman aşımı süresi - dakika cinsinden
    $timeout_limit = "0";

    ## Hata mesajlarının ekrana basılması için entegrasyon ve test sürecinde 1 olarak bırakın. Daha sonra 0 yapabilirsiniz.
    $debug_on = 0; // 1 di degisti

    ## Mağaza canlı modda iken test işlem yapmak için 1 olarak gönderilebilir.
    $test_mode = 0;

    $no_installment = 0; // Taksit yapılmasını istemiyorsanız, sadece tek çekim sunacaksanız 1 yapın

    ## Sayfada görüntülenecek taksit adedini sınırlamak istiyorsanız uygun şekilde değiştirin.
    ## Sıfır (0) gönderilmesi durumunda yürürlükteki en fazla izin verilen taksit geçerli olur.
    $max_installment = 0;

    $currency = "TL";

    ####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
    $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;
    $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
    $post_vals=array(
            'merchant_id'=>$merchant_id,
            'user_ip'=>$user_ip,
            'merchant_oid'=>$merchant_oid,
            'email'=>$email,
            'payment_amount'=>$payment_amount,
            'paytr_token'=>$paytr_token,
            'user_basket'=>$user_basket,
            'debug_on'=>$debug_on,
            'no_installment'=>$no_installment,
            'max_installment'=>$max_installment,
            'user_name'=>$user_name,
            'user_address'=>$user_address,
            'user_phone'=>$user_phone,
            'merchant_ok_url'=>$merchant_ok_url,
            'merchant_fail_url'=>$merchant_fail_url,
            'timeout_limit'=>$timeout_limit,
            'currency'=>$currency,
            'test_mode'=>$test_mode
        );

    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1) ;
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

     // XXX: DİKKAT: lokal makinanızda "SSL certificate problem: unable to get local issuer certificate" uyarısı alırsanız eğer
     // aşağıdaki kodu açıp deneyebilirsiniz. ANCAK, güvenlik nedeniyle sunucunuzda (gerçek ortamınızda) bu kodun kapalı kalması çok önemlidir!
     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $result = @curl_exec($ch);

    if(curl_errno($ch))
        die("PAYTR IFRAME connection error. err:".curl_error($ch));

    curl_close($ch);

    $result=json_decode($result,1);

    if($result['status']=='success'){
        $token=$result['token'];
//burasi insert

        


    }


    else
        die("PAYTR IFRAME failed. reason:".$result['reason']);
    #########################################################################



   

    return view('frontend.sepet.sonuc')
    ->with(['deg' => $deg,'token' => $token]);


    








        











//burasi ikinci kisim

$merchant_key   = 'zTdMc8P92GwRYCrx';
$merchant_salt  = 'nFHbUbwMZepcYu6E';
###########################################################################

####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
#
## POST değerleri ile hash oluştur.
$hash = base64_encode( hash_hmac('sha256', $post['merchant_oid'].$merchant_salt.$post['status'].$post['total_amount'], $merchant_key, true) );
#
## Oluşturulan hash'i, paytr'dan gelen post içindeki hash ile karşılaştır (isteğin paytr'dan geldiğine ve değişmediğine emin olmak için)
## Bu işlemi yapmazsanız maddi zarara uğramanız olasıdır.
if( $hash != $post['hash'] ){





die('PAYTR notification failed: bad hash');
###########################################################################

## BURADA YAPILMASI GEREKENLER
## 1) Siparişin durumunu $post['merchant_oid'] değerini kullanarak veri tabanınızdan sorgulayın.
## 2) Eğer sipariş zaten daha önceden onaylandıysa veya iptal edildiyse  echo "OK"; exit; yaparak sonlandırın.

/* Sipariş durum sorgulama örnek
$durum = SQL
if($durum == "onay" || $durum == "iptal"){
    echo "OK";
    exit;
}
*/












if( $post['status'] == 'success' ) { ## Ödeme Onaylandı

## BURADA YAPILMASI GEREKENLER
## 1) Siparişi onaylayın.
## 2) Eğer müşterinize mesaj / SMS / e-posta gibi bilgilendirme yapacaksanız bu aşamada yapmalısınız.
## 3) 1. ADIM'da gönderilen payment_amount sipariş tutarı taksitli alışveriş yapılması durumunda
## değişebilir. Güncel tutarı $post['total_amount'] değerinden alarak muhasebe işlemlerinizde kullanabilirsiniz.
//burasi insert


echo "burası benim sayfam";



}



else { ## Ödemeye Onay Verilmedi

## BURADA YAPILMASI GEREKENLER
## 1) Siparişi iptal edin.
## 2) Eğer ödemenin onaylanmama sebebini kayıt edecekseniz aşağıdaki değerleri kullanabilirsiniz.
## $post['failed_reason_code'] - başarısız hata kodu
## $post['failed_reason_msg'] - başarısız hata mesajı

echo "İşlem hatalıdır";

}



## Bildirimin alındığını PayTR sistemine bildir.
echo "OK";





exit;

}

?>




//burasi ikinci kisim












    </body>
</html>