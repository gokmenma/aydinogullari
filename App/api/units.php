<?php 

require_once dirname(__DIR__, 2) . "/bootstrap.php";
use App\Model\UnitsModel;

$Units = new UnitsModel();

if($_POST['action'] == 'getUnits')
{
    $units = $Units->getUnits();

    $res = [
        'status' => "success",
        'data' => $units
    ];
    echo json_encode($res);
}
