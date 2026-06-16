<?php if ($satirNo > 0) {
    ?>
    <td style="width: 10px;"><a href="#" class="btn btn-sm"><i class="fa fa-arrows-alt"></i></a></td>

    <td class="app-item-action" style="max-width: 20px;">
        <a type="button" class="sil btn btn-sm btn-danger text-white"><i class="fa fa-trash"></i></a>
    </td>

    <!--Sırano-->
    <td class="app-item-number">
        <input class="form-control" type="text" value="<?php echo $satirNo; ?>">
    </td>
    <!--Sırano-->

    <!-- Stok Kodu -->
    <td class="app-item-stock"><input type="text" id="stokKodu<?php echo $satirNo; ?>" value="<?php echo $stokKodu; ?>"
            name="stokKodu[]" class="form-control" placeholder="Stok Kodu giriniz!">
    </td>
    <!-- Stok Kodu -->

    <td class="app-item-name">
        <!-- Button trigger modal -->
        <div class="input-group m-0">

            <input type="text" class="urunAdi form-control" name="urunAdi[]" id="urunAdi<?php echo $satirNo; ?>"
                value="<?php echo $urunAdi; ?>" placeholder="Ürün adını giriniz!">
            <button type="button" id="<?php echo $satirNo; ?>" class="btn btn-sm btn-info selectProduct"
                data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="fa fa-plus-circle"></i>
            </button>
        </div>

    <?php } ?>
    <!-- Modal -->
    <div class="modal show" id="staticBackdrop">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel">Listeden ürün seçiniz!</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php generateProductSelect("productName[]", $item["productID"] ?? null) ?>
                    <input type="hidden" id="rowID">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-danger" onclick="getProductInfoPurchase()">Seç</button>
                </div>
            </div>
        </div>
    </div>

</td>

<?php if ($satirNo > 0) {
    ?>

    <!-- MİKTAR -->
    <td class="app-item-amount">
        <input type="number" autocomplete="off" required id="amount" name="amount[]" value="<?php echo $amount; ?>" class="Adet form-control">
    </td>
    <!-- MİKTAR -->

    <!-- ÖLÇÜ BİRİMLERİ -->
    <td class="app-item-unit">
        <?php OlcuBirimleri('unit[]', $unit, "required", "unit" . $satirNo) ?>
    </td>
    <!-- ÖLÇÜ BİRİMLERİ -->

    <!-- FİYAT -->
    <td class="app-item-price">
    <input required id="buyprice<?php echo $satirNo; ?>" name="buyprice[]" type="number"
        value="<?php echo $buyprice; ?>" class="form-control mr-1" autocomplete="off">

</td>
    <!-- FİYAT -->

    <!-- PARA BİRİMLERİ -->
    <td class="app-item-cur">
        <?php ParaBirimleri("buycur[]", $buycur, "buycur" . $satirNo) ?>
    </td>
    <!-- PARA BİRİMLERİ -->


    <!-- AÇIKLAMA -->
        <td>
            <input type="text" class="form-control" style="min-width: 150px; width: 100%;" 
            name="rowdescription[]" value="<?php echo $rowdescription ;?>">
        </td>
    <!-- AÇIKLAMA -->

<?php } ?>