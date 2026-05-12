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
    exit;
}

if ($_POST['action'] == 'deleteItemFile') {
    $purchaseId = $_POST['purchaseId'];
    $itemId = $_POST['itemId'];

    try {
        // Clear both image and excel_file fields as we merged them in the UI
        $sql = $ac->prepare('UPDATE purchase_items SET image = NULL, excel_file = NULL WHERE id = ?');
        $sql->execute(array($itemId));

        $status = 'success';
        $message = 'Dosya başarıyla silindi.';
    } catch (PDOException $ex) {
        $status = 'error';
        $message = $ex->getMessage();
    }

    $res = array(
        'status' => $status,
        'message' => $message
    );
    echo json_encode($res);
    exit;
}

if ($_POST['action'] == 'savePurchases') {

    $id = $_POST['id'];
    $demand = $_POST['demand'] ?? 0;
    
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
            'payment_period' => $_POST['payment_period'] ?? '',
            'payment_date' => $_POST['payment_date'] ?? '',
            'description1' => $_POST['description1'],
            'description2' => $_POST['description2'] ?? '',
            'altToplam' => $_POST['altToplam'],
            'vadeGun' => $_POST['vadeGun'] ?? 0,
            'Dollar' => $_POST['Dollar'] ?? 0,
            'Euro' => $_POST['Euro'] ?? 0,
            'DolarTotal' => $_POST['DolarAlttoplam'] ?? 0,
            'EuroTotal' => $_POST['EuroAlttoplam'] ?? 0,
            'TLTotal' => $_POST['TLAlttoplam'] ?? 0,
            'Kdv' => $_POST['Kdv'] ?? 0,
            'iskonto' => $_POST['iskonto'] ?? 0,
            'ToplamTL' => $_POST['ToplamTL'] ?? 0,
            'state' => $_POST['state'] ?? 1,
            'invoice_date' => $_POST['invoice_date'] ?? '',
            'invoice_number' => $_POST['invoice_number'] ?? '',
            'type' => $_POST['type'] ?? 0
            
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
            // Mevcut ürünleri al (resim/excel yollarını korumak için)
            $existingItems = $Purchase->getPurchaseItems($id);
            
            //Satın alma'nın ürünlerini siler
            $Purchase->deletePurchaseItems($id);

            //Satın alma'nın ürünlerini ekler
            for ($i = 0; $i < count($urunAdi); $i++) {
                $itemData = [
                    'purID' => $lastInsertId,
                    'product' => $urunAdi[$i],
                    'stokKodu' => $_POST['stokKodu'][$i],
                    'amount' => $_POST['amount'][$i],
                    'unit' => $_POST['unit'][$i],
                    'price' => $_POST['price'][$i],
                    'currency' => $_POST['currency'][$i],
                    'rowdescription' => $_POST['rowdescription'][$i] ?? ''
                ];

                // Tek bir dosya yükleme işlemi (Resim veya Excel)
                if (isset($_FILES["row_file_$i"]) && $_FILES["row_file_$i"]['error'] == 0) {
                    $fileInfo = pathinfo($_FILES["row_file_$i"]['name']);
                    $ext = strtolower($fileInfo['extension']);
                    $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES["row_file_$i"]['name']);
                    $targetPath = "uploads/purchases/" . $fileName;
                    
                    if (move_uploaded_file($_FILES["row_file_$i"]['tmp_name'], dirname(__DIR__, 2) . "/" . $targetPath)) {
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $docExtensions = ['xls', 'xlsx', 'csv', 'pdf'];
                        
                        if (in_array($ext, $imageExtensions)) {
                            $itemData['image'] = $targetPath;
                        } elseif (in_array($ext, $docExtensions)) {
                            $itemData['excel_file'] = $targetPath;
                        }
                    }
                } else {
                    // Yeni dosya yoksa mevcutları koru
                    if (isset($existingItems[$i]->image)) {
                        $itemData['image'] = $existingItems[$i]->image;
                    }
                    if (isset($existingItems[$i]->excel_file)) {
                        $itemData['excel_file'] = $existingItems[$i]->excel_file;
                    }
                }

                $Purchase->savePurchaseItems($itemData);
            }
        }

        

      

//Geriye mesaj ve durumu döndür
        $status = "success";
        $message = "İşlem başarıyla tamamlandı." ;
        $id = $lastInsertId;


        //Bir sonraki sipariş numarasını belirle
        if ($_POST['type'] == 2) {
             Helper::setDefineNumber('price_request');
        } else {
             Helper::setDefineNumber('purchase');
        }

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


