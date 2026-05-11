<?php

namespace App\Model;
use App\Model\BaseModel;
use PDO;
class ProductModel extends BaseModel
{

    protected $table = 'products';
    protected $item_table = 'product_items';

    public function __construct()
    {
         parent::__construct($this->table);
    }

    /** Ürünleri birimi ile beraber getirir
     * @return object[]
     */
    public function getAllWithUnits()
    {
        $sql = $this->db->prepare("SELECT p.*, u.title AS birim FROM $this->table p
                                          LEFT JOIN units u ON u.id = p.Birimi
                                          ORDER BY p.Adi ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }   


}