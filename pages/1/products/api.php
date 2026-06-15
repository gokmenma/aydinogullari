<?php
require_once dirname(__DIR__, 3) . "/bootstrap.php";


use App\Helper\Helper;
use App\Helper\Security;
use App\Model\ProductModel;

$Products = new ProductModel();


if ($_POST['action'] == 'save-product') {

    $id = $_POST['id'] !=  0 ? Security::decrypt($_POST['id']) : 0;

    try {


        $data = [
            'id' => $id,
            "StokKodu" => $_POST['StokKodu'],
            "Adi" => $_POST['urunAdi'],
            "Birimi" => $_POST['Birimi'],
            "AlisFiyati" => $_POST['AlisFiyati'],
            "AlisParaBirimi" => $_POST['AlisParaBirimi'],
            "SatisFiyati" => $_POST['SatisFiyati'],
            "SatisParaBirimi" => $_POST['SatisParaBirimi'],
            "Aciklama" => $_POST['Aciklama'] ?? '',
        ];

        $lastInsertId = $Products->save($data) ?? $_POST['id'];

        $msg = $id == 0 ? "kaydedildi" : "güncellendi";
        $status = "success";
        $message = "Ürün/Hizmet başarıyla " . $msg;


    } catch (PDOException $ex) {
        $status = "error";
        $message = $ex->getMessage();
    }

    $res = [
        "status" => $status,
        "message" => $message,
        "data" => $data

    ];

    echo json_encode($res);
}

//Ürün silme

if ($_POST['action'] == 'delete-product') {
    $id = $_POST['id'];

    try {
        $Products->delete(Security::decrypt($id));
        $status = "success";
        $message = "Ürün başarıyla silindi.";
    } catch (PDOException $ex) {
        $status = "error";
        $message = $ex->getMessage();
    }

    $res = [
        "status" => $status,
        "message" => $message
    ];

    echo json_encode($res);
}