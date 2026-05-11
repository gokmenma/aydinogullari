<?php 

namespace App\Model;

use PDO;
use App\Model\BaseModel;


class KesifModel extends BaseModel
{
    protected $table = 'kesifler';

    public function __construct()
    {
       parent::__construct($this->table);
    }

    /**
     * Tüm kesifleri getir (silinmemiş olanları)
     */
    public function getAllActive()
    {
        $sql = $this->db->prepare("SELECT k.*, u.username AS kullanici_adi FROM $this->table k
                                          LEFT JOIN users u ON u.id = k.kayit_yapan
                                          WHERE k.silinme_tarihi IS NULL
                                          ORDER BY k.kesif_tarihi DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * ID'ye göre keşif getir (silinmemiş olanları)
     */
    public function findActive($id)
    {
        $sql = $this->db->prepare("SELECT k.*, 
                                          u1.username AS kullanici_adi,
                                          u2.username AS guncelleyen_adi
                                   FROM $this->table k
                                   LEFT JOIN users u1 ON u1.id = k.kayit_yapan
                                   LEFT JOIN users u2 ON u2.id = k.guncelleyen_kullanici
                                   WHERE k.id = ? AND k.silinme_tarihi IS NULL");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Keşifi sil (Soft Delete)
     */
    public function softDelete($id, $userId)
    {
        $sql = $this->db->prepare("UPDATE $this->table SET silinme_tarihi = NOW(), silen_kullanici = ? WHERE id = ?");
        return $sql->execute([$userId, $id]);
    }

    /**
     * Firma adına göre keşifleri ara
     */
    public function searchByFirma($firma)
    {
        $sql = $this->db->prepare("SELECT * FROM $this->table WHERE firma LIKE ? AND silinme_tarihi IS NULL ORDER BY kesif_tarihi DESC");
        $sql->execute(["%{$firma}%"]);
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

}