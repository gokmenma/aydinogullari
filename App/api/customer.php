<?php

require_once dirname(__DIR__, 2) . "/bootstrap.php";


use App\Helper\Helper;
use App\Model\CustomerModel;




$Customer = new CustomerModel();

if ($_POST['action'] == 'create') {
    $id = $_POST['company_id'];
    try {
        $data = [
            'id' => $id,
            'company' => $_POST['company'],
            'email' => $_POST['cemail'],
            'address' => $_POST['customer_address'],
            'city' => $_POST['il'],
            'ilce' => $_POST['ilce'],
            'cdesc' => $_POST['cdesc'],
            'gsm' => $_POST['cgsm'],
            'yetkili' => $_POST['yetkili'],
            'grp' => $_POST['categoryName'],
            'OdemeVade' => $_POST['vade'],
            'region' => $_POST['region'],
            'represant' => $_POST['represant'],
            'updater' => $_SESSION['lid']
        ];
        $lastInsertId = $Customer->save($data) ?? $id;
        $status = 'success';
        $message = 'Firma işlemi başarı ile tamamlandı!';


    } catch (Exception $e) {
        $status = 'error';
        $message = $e->getMessage();
    }
    $res = [
        'status' => $status,
        'message' => $message
    ];
    echo json_encode($res);
}

