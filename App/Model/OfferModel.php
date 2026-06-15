<?php
namespace App\Model;

use PDO;
use Exception;
use PDOException;
use App\Model\BaseModel;
use App\Helper\Helper;


class OfferModel extends BaseModel 
{

    protected $table = 'offers';
    protected $productTable = 'offermatters';

    public function __construct()
    {
        parent::__construct($this->table);
    }


    /**
     * Teklifleri Listelemek için firma adıyla birlikte getirir
     * 
     */
    public function getOffersWithCompanyName($sablonlari_goster = false)

    {
        $condition = $sablonlari_goster ? "WHERE o.is_template = 1" : "WHERE o.is_template = 0";
        $sql = $this->db->prepare("SELECT 
                                            o.*,  
                                            c.company as company_name, 
                                            c.id as customer_id,
                                            u.username as creator_name,
                                            CASE
                                                WHEN o.statu = 1 THEN 'Bekliyor'
                                                WHEN o.statu = 2 then 'Tamamlandı'
                                            END AS durum
                                        FROM 
                                            $this->table o
                                        LEFT JOIN 
                                            customers c ON o.cid = c.id
                                        LEFT JOIN 
                                            users u ON o.creativer = u.id");
                                               
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function saveOfferProduct($data)
    {
        $this->table = $this->productTable;
        return $this->save($data);
    }

    public function getOfferProducts($id)
    {
        $this->table = $this->productTable;
        $sql = $this->db->prepare("SELECT * FROM $this->table where oid = ?");
        $sql->execute([$id]);
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function deleteOfferProduct($id)
    {
        $this->table = $this->productTable;
        $sql = $this->db->prepare("DELETE FROM $this->table where oid = ?");
        $sql->execute([$id]);
        return $sql->rowCount();
    }

    //Teklifi Kopyala
    public function copyOffer($id)
    {

        $offer = $this->find($id);
        //Offer'ın tüm alanlarını döngüyle al
        foreach ($offer as $key => $value) {
            //id ve created_at hariç diğer alanları yeni offer'a ekle
            if ($key != 'id' && $key != 'created_at' && $key != 'offerNumber') {
                $data[$key] = $value;
            } else if ($key == 'offerNumber') {
                //Teklif numarasını oluştur
                $data[$key] = Helper::generateNumber('offer', 'TK');
            }
            //is_template alanını 0 yap
            $data['is_template'] = 0;

            //oluşturan kullanıcıyı al
            $data['creativer'] = $_SESSION["lid"];

            //tarihi bugn yap
            $data['created_at'] = date('Y-m-d H:i:s');

            //Güncelleme tarihini de bugün yap
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        //Offer'ı kaydet
        $newOfferId = $this->save($data);
        Helper::setDefineNumber('offer');

        //Offer'a ait ürünleri al
        $offerProducts = $this->getOfferProducts($id);
        //Ürünleri döngüyle al,oid alanına yeni offer'ın id'sini ekle
        foreach ($offerProducts as $product) {
            //id ve created_at hariç diğer alanları yeni offer'a ekle
            foreach ($product as $key => $value) {
                if ($key != 'id' && $key != 'created_at' && $key != 'oid') {
                    $productData[$key] = $value;
                } else if ($key == 'oid') {
                    $productData[$key] = $newOfferId;
                }
            }
            //Ürünü kaydet
            $this->saveOfferProduct($productData);
        }
    }

    //convertToTry
    public function convertToTry($id)
    {
        $offer = $this->find($id);

        // offer yoksa hata döndür
        if (!$offer) {
            $status = 'error';
            $message = 'Teklif bulunamadı.';

            $res = [
                'status' => $status,
                'message' => $message
            ];
            return json_encode($res);
        }


        $offerProducts = $this->getOfferProducts($id);
        $alt_toplam = 0;
        foreach ($offerProducts as $product) {
            // Para birimi TL ise devam et
            if ($product->salecur == 'TRY') {
                $alt_toplam += $product->total_price;
                continue;
            }


            $currency = $product->salecur == "EUR" ? $offer->curEuro : $offer->curDollar;
            //Ürünün alış fiyatını TL'ye çevir
            $buyprice = $product->buyprice * $currency;

            // Ürünün satış fiyatını TL'ye çevir
            $saleprice = $product->saleprice * $currency;

            //Alt Toplam Hesapla
            $satır_toplam = $product->amount * $saleprice;
            $data = [
                "id" => $product->id,
                'buyprice' => $buyprice,
                'buycur' => "TRY",
                "saleprice" => $saleprice,
                "salecur" => 'TRY',
                "total_price" => $satır_toplam,

            ];


            $this->saveOfferProduct($data);

            //Alt toplamı hesapla
            $alt_toplam += $satır_toplam;
        }

        //Teklifin toplamını güncelle
        $iskonto = ($offer->euro_iskonto * $offer->curEuro) + ($offer->dolar_iskonto * $offer->curDollar) + ($offer->tl_iskonto);
        $kdv = ($offer->euro_kdv * $offer->curEuro) + ($offer->dolar_kdv * $offer->curDollar) + ($offer->tl_kdv);
        $tl_toplam = $alt_toplam + $kdv - $iskonto;
        $data = [
            "id" => $id,
            'euro_ara_toplam' => 0,
            'euro_alt_toplam' => 0,
            'dolar_ara_toplam' => 0,
            'dolar_alt_toplam' => 0,
            'tl_alt_toplam' => $alt_toplam,
            "euro_kdv" => 0,
            "dolar_kdv" => 0,
            "tl_kdv" => $kdv,
            "euro_kdvli_toplam" => 0,
            "dolar_kdvli_toplam" => 0,
            "tl_kdvli_toplam" => $tl_toplam,
            "tl_toplam_karsilik" => $tl_toplam,
        ];

        $this->table = 'offers';
        $this->save($data);


        $res = [
            'status' => 'success',
            'message' => 'Teklif TRY\'ye çevrildi.'
        ];
        return json_encode($res);
    }


    /*Bekleyen ve tamamlanan teklif sayılarını döndürür 
    * return int
    */
    public function getOfferCountWaitingAndDone()
    {
        $sql = $this->db->prepare("SELECT
                                            COUNT(CASE WHEN statu = 1 THEN 1 END) AS bekleyen_teklif,
                                            COUNT(CASE WHEN statu = 2 THEN 1 END) AS tamamlanan_teklif
                                        FROM offers;");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_OBJ);
    }

    /* Teklif Silme
    * return int
    */
    public function deleteOffer($id)
    {
        try {
            $this->db->beginTransaction();

            // Teklifin ürünlerini sil
            $this->deleteOfferProduct($id);
            // Teklifi sil
            $sql = $this->db->prepare("DELETE FROM offers WHERE id = :id");
            $sql->bindParam(':id', $id, PDO::PARAM_INT);
            $sql->execute();
            // Eğer silme işlemi başarılıysa commit et
            $this->db->commit();
            // Silinen satır sayısını döndür
            return $sql->rowCount();
        } catch (PDOException $ex) {
            // Hata durumunda rollback yap
            $this->db->rollBack();
            // Hata mesajını döndür
            throw new Exception("Teklif silinirken hata oluştu: " . $ex->getMessage());
        }
    }

    /*Teklfifin durumunu getir 
    * @param int $id Teklif ID'si
    * @return int 1: Bekliyor, 2: Tamamlandı
    */
    public function getOfferStatus($id)
    {
        $sql = $this->db->prepare("SELECT statu FROM $this->table WHERE id = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
        
        if ($result) {
            return (int)$result->statu;
        } else {
            throw new Exception("Teklif bulunamadı.");
        }
    }

    //Teklif Numarası Var mı Kontrol
    public function checkOfferNumberExists($offerNumber, $excludeId = 0)
    {
        if ($excludeId > 0) {
            $sql = $this->db->prepare("SELECT COUNT(*) as count 
                                              FROM $this->table 
                                              WHERE offerNumber = :offerNumber AND id != :excludeId");
            $sql->bindParam(':offerNumber', $offerNumber, PDO::PARAM_STR);
            $sql->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        } else {
            $sql = $this->db->prepare("SELECT COUNT(*) as count 
                                          FROM $this->table 
                                          WHERE offerNumber = :offerNumber");
            $sql->bindParam(':offerNumber', $offerNumber, PDO::PARAM_STR);
        }
        
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
        
        return $result->count > 0;
    }

}
