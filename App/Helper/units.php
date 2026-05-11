<?php 
namespace App\Helper;

use Database\Db;
use PDO;
class Units extends Db
{

    protected $table = 'units';

    public function getUnits()
    {
   
        
        $sql = $this->db->prepare("SELECT * FROM units WHERE statu = 1");
        $sql->execute();
        $units = $sql->fetchAll(PDO::FETCH_OBJ);
        
        return $units;
    }
}