<?php 

namespace App\Model;

use App\Model\BaseModel;

use App\Helper\Helper;

class DocumentModel extends BaseModel
{
    protected $table = 'evraktakip';


    public function __construct()
    {
        parent::__construct($this->table);
    }

  
    //evrakı teslim alındı yap
    public function receiveDocument($id)
    {
        $sql = $this->db->prepare("UPDATE $this->table SET teslimalan = :teslimalan, 
                                                                  teslimalmatarihi = :teslimalmatarihi
                                                                  WHERE id = :id");
        $sql->execute([
            'id' => $id,
            'teslimalan' => $_SESSION['uid'],
            'teslimalmatarihi' => date('Y-m-d H:i:s')
          
        ]);
        return $sql->rowCount();
    }

 
}