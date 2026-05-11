<?php 

require_once dirname(__DIR__, 2) . "/bootstrap.php";
use App\Helper\Helper;

//Birimleri veritabanından getir
if( $_POST['action'] == 'getUnits') {
        
    $res = array(
        'status' => 'success',
        'units' => $units
    );
    echo json_encode($res);
}
