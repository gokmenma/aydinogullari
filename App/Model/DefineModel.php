<?php 
namespace App\Model;

use App\Model\BaseModel;

use App\Helper\Helper;

class DefineModel extends BaseModel
{
    protected $table = 'units';


    public function __construct()
    {
        parent::__construct($this->table);
    }

    //Gelen id'den birim isimlerini getir
    public function getUnitName($id)
    {
        $unit = $this->find($id);
        return $unit->title;
    }
 
}