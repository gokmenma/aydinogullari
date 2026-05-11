<?php 
namespace App\Model;

use App\Model\BaseModel;
class CustomerModel extends BaseModel
{
    protected $table = 'customers';

    public function __construct()
    {
       parent::__construct($this->table);
    }


}
