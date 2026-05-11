

    <?php
    require_once __DIR__ . "/bootstrap.php";
    //require_once "configs/index.php";
    // require_once "App/Helper/security.php";
    // require_once "App/Helper/date.php";
    // require_once "App/Model/ServiceModel.php";
    // require_once "App/Helper/customer.php";


    use App\Helper\Security;
    use App\Helper\Date;
    use App\Helper\customer;
    use App\Model\ServiceModel;

    $Services = new ServiceModel();
    $id = Security::decrypt($_GET["id"]);

    $service = $Services->find($id);


    $customer = customer::getCustomer($service->pcid);


    $document = $customer->company . "-" . $service->service_number;
    $address = $customer->address;
    $encodedAddress = urlencode($address);
    $googleMapsLink = "https://www.google.com/maps/search/?api=1&query=" . $encodedAddress;



    ?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <title><?php echo $document  ; ?></title>
    <?php   
    include 'include/head.php';
    ?>
</head>

<body>
    <div class="container pd-ltr-10 xs-pd-10-10">
        <div class="content pd-20 bg-white border-radius-16 box-shadow">
            <div class="clearfix mb-30">
                <div class="pull-left">
                    <h4 class="text-blue m-0">Servis Bilgileri</h4>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Servis Numarası</label>
                </div>
                <div class="col-md-8">
                    <?php echo $service->service_number; ?></p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Servis Oluşturma Tarihi</label>
                </div>
                <div class="col-md-8">
                    <?php echo $service->pregdate; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Servis Oluşturan</label>
                </div>
                <div class="col-md-8">
                    <?php echo $service->pcreativer; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Servis Konusu</label>
                </div>
                <div class="col-md-8">
                    <?php echo $service->servicestype; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container pd-ltr-10 xs-pd-10-10">
        <div class="content pd-20 bg-white border-radius-16 box-shadow">
            <div class="clearfix mb-30">
                <div class="pull-left">
                    <h4 class="text-blue m-0">Firma Bilgileri</h4>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Firma</label>
                </div>
                <div class="col-md-8">
                    <?php echo $customer->company; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Firma Yetkilisi</label>
                </div>
                <div class="col-md-8">
                    <?php echo $customer->yetkili; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Firma Adres/Telefon</label>
                </div>
                <div class="col-md-8">

                    <a href="<?php echo $googleMapsLink ?>" target="_blank"><?php echo $address; ?>
                    </a><?php echo " / " . $customer->gsm; ?>
                    <p>

                        <small ><a href="<?php echo $googleMapsLink ?>" class="text-danger" target="_blank">Haritada görüntülemek için tıklayınız!</a></small>
                    </p>
                    <p>
                        
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container pd-ltr-10 xs-pd-10-10">
        <div class="content pd-20 bg-white border-radius-16 box-shadow">
            <div class="clearfix mb-30">
                <div class="pull-left">
                    <h4 class="text-blue m-0">Servis Detay Bilgileri</h4>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">İşin Tanımı</label>
                </div>
                <div class="col-md-8">
                    <?php echo $service->pdesc; ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Firmaya Giriş(Saat Detayı)</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="firmayaGiris" value="">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Firmadan Çıkış(Saat Detayı)</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="firmadanCikis" value="">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Personel Miktarı :</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="personelMiktari" value="">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Varsa Gecikme Nedeni :</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="gecikmeNedeni" value="">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">Servis Sonucu</label>
                </div>
                <div class="col-md-8">
                    <textarea class="form-control" name="servisSonucu" rows="4"></textarea>
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="text-blue m-0">İşi Teslim Eden Personel</label>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="teslimEdenPersonel" value=""
                        placeholder="Ad soyad giriniz">
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-md-4"></div>
                <div class="col-md-8">
                    <button type="submit" class="btn btn-primary w-100">İşi Teslim Et</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>