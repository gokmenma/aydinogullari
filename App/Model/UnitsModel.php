<?php 

namespace App\Model;
use PDO;
use App\Model\BaseModel;

class UnitsModel extends BaseModel
{
    protected $table = 'units';
    public function __construct()
    {
        parent::__construct($this->table);
    }


    public function getUnits()
    {
        //type' i 1 olanları getir
        $sql = $this->db->prepare("SELECT * FROM $this->table WHERE statu = 1");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    //Gelen değere göre id'yi bul
    public function getUnitId($title)
    {
        $sql = $this->db->prepare("SELECT id FROM $this->table WHERE title = ?");
        $sql->execute([$title]);
        return $sql->fetch(PDO::FETCH_OBJ);
    }
}