<?php

namespace App\Model;

use App\Model\BaseModel;
use PDO;

class PurchaseModel extends BaseModel
{

    protected $table = 'purchases';
    protected $item_table = 'purchase_items';

    public function __construct()
    {
         parent::__construct($this->table);
    }

    //Satın alam'nın ürünlerini getirir
    public function getPurchaseItems($id)
    {
        $sql = $this->db->prepare("SELECT * FROM $this->item_table WHERE purID = ?");
        $sql->execute([$id]);
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    //Satın alam'nın ürünlerini siler
    public function deletePurchaseItems($id)
    {
        $sql = $this->db->prepare("DELETE FROM $this->item_table WHERE purID = ?");
        $sql->execute([$id]);
    }

    //Satın alam'nın ürünlerini ekler
    public function savePurchaseItems($data)
    {
        $this->table = $this->item_table;
        return $this->save($data);
    }

}
