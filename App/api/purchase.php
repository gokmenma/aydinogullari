<?php
require_once dirname(__DIR__, 2) . "/bootstrap.php";

use App\Helper\Helper;
use App\Model\PurchaseModel;

$Purchase = new PurchaseModel();

if ($_POST['action'] == 'doneDemand') {
    $id = $_POST['id'];

    try {
        $sql = $ac->prepare('UPDATE purchases SET state = 2 WHERE id = ?');
        $sql->execute(array($id));

        $status = 'success';
        $message = 'Talep başarıyla tamamlandı.';
    } catch (PDOException $ex) {
        $status = 'error';
        $message = $ex->getMessage();
    }

    $res = array(
        'status' => $status,
        'message' => $message
    );
    echo json_encode($res);
}

if ($_POST['action'] == 'savePurchases') {

    $id = $_POST['id'];
    $demand = $_POST['demand'];
    
    //Eğer satın alma talebinden geliyorsa yeni bir satın alma işlemi oluşturulacak
    if ($demand == 1) {
        $id = 0;
    }

   
    try {
         $data = [
            'id' => $id,
            'siparisNo' => $_POST['siparisNo'],
            'companyID' => $_POST['customers'],
            'currency' => $_POST['cur_type'],
            'deadline' => $_POST['deadline'],
            'payment_period' => $_POST['payment_period'],
            'payment_date' => $_POST['payment_date'],
            'description1' => $_POST['description1'],
            'description2' => $_POST['description2'],
            'altToplam' => $_POST['altToplam'],
            'vadeGun' => $_POST['vadeGun'],
            'Dollar' => $_POST['Dollar'],
            'Euro' => $_POST['Euro'],
            'DolarTotal' => $_POST['DolarAlttoplam'],
            'EuroTotal' => $_POST['EuroAlttoplam'],
            'TLTotal' => $_POST['TLAlttoplam'],
            'Kdv' => $_POST['Kdv'],
            'iskonto' => $_POST['iskonto'],
            'ToplamTL' => $_POST['ToplamTL'],
            'state' => $_POST['state'] ?? 1,
            'invoice_date' => $_POST['invoice_date'],
            'invoice_number' => $_POST['invoice_number'],
            'type' => 2
            
        ];   

        if ($id == 0) {
            $data['creator'] = $_SESSION['lid'];
      
        } else {
            $data['updater'] = $_SESSION['lid'];
        }

        $lastInsertId = $Purchase->save($data) ?? $id;


        $talepSipariseDonuyor = $_POST['satinAlmaTalebiniKapat'] ?? 0;
        $talep_id = $_POST['talep_id'] ?? 0;

        if( $talepSipariseDonuyor == 1) {
            $data = [
                'id' => $talep_id,
                'state' => 2, //Siparişe dönüştürülmüş
                'talep_id' => $talep_id,
            ];
            $Purchase->save($data);
        }

        $urunAdi = $_POST['urunAdi'];
        if (isset($urunAdi)) {
            //Satın alma'nın ürünlerini siler
            $Purchase->deletePurchaseItems($id);

            //Satın alma'nın ürünlerini ekler
            for ($i = 0; $i < count($urunAdi); $i++) {
                $Purchase->savePurchaseItems([
                    'purID' => $lastInsertId,
                    'product' => $urunAdi[$i],
                    'stokKodu' => $_POST['stokKodu'][$i],
                    'amount' => $_POST['amount'][$i],
                    'unit' => $_POST['unit'][$i],
                    'price' => $_POST['price'][$i],
                    'currency' => $_POST['currency'][$i]

                ]);
            }
        }

        

      

//Geriye mesaj ve durumu döndür
        $status = "success";
        $message = "Satın alma işlemi başarıyla tamamlandı." ;
        $id = $lastInsertId;


        //Bir sonraki sipariş numarasını belirle
        Helper::setDefineNumber('purchase');

    } catch (PDOException $ex) {
        $status = "error";
        $message = $ex->getMessage();
    }

    $res = [
        'status' => $status,
        'message' => $message,
        'id' => $id
    ];

    echo json_encode($res);

}


