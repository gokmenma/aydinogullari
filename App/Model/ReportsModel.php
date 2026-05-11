<?php 

namespace App\Model;
use App\Model\BaseModel;




class ReportsModel extends BaseModel
{
    protected $table = 'reports';

    public function __construct()
    {
       parent::__construct($this->table);
    }

    

}
