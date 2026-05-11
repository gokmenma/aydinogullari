<?php


use App\Model\ReportControlModel;


$control = new ReportControlModel();


use App\Helper\Date;

$month = $_POST['control_month'] ?? Date::getThisMonth();
$year = $_POST['control_year'] ?? Date::getThisYear();
$controlList = $control->getReportFillingList($month, $year);

// echo 'bu ay : ' . $month . ' bu yıl : ' . $year;

//  echo "sayı : " . count($controlList);

?>

<form method="POST" action="index.php?p=reports/filling-list">
    <div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue">Cihaz Dolum Listesi</h4>
                <p>Dolum tarihi gelmiş cihaz listesi</p>

            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="">Ay</label>
                <?php echo Date::getMonthSelect('control_month', $month) ?>
            </div>
            <div class="col-md-4">
                <label for="">Yıl</label>
                <?php echo Date::getYearSelect('control_year', $year) ?>

            </div>
            <div class="col-md-4 ">
                <label for="">Filtre</label>
                <div class="d-block w-100">

                    <button class="btn btn-primary">Filtrele</button>
                    <button type="button" id="filling_list-toxls" class="btn btn-secondary">Excele Aktar</button>
                    
                </div>
            </div>

        </div>
    </div>
</form>

<div class="pd-20 bg-white border-radius-16 box-shadow mb-30">
    <div class="clearfix">
        <div class="pull-left mb-4">
            <h4 class="text-blue">Ürünler</h4>
        </div>

    </div>
<style>
    .table-responsive{
        padding :5px !important;
    }
</style>
    <div class="table-responsive">

        <table class="table-hover table table-bordered data-table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Firma Adı</th>
                    <th scope="col">Rapor No</th>
                    <th scope="col">Cihaz No</th>
                    <th scope="col">Bulunduğu Bölge</th>
                    <th scope="col">Ay</th>
                    <th scope="col">Yıl</th>
                   
                </tr>
            </thead>
            <tbody>

                <?php 
                 $i = 1;
                foreach ($controlList as $list) : 
                   
                    ?>
                    <tr>
                        <td scope="row"><?= $i ?></td>
                        <td><?= $list->firma_adi ?></td>
                        <td ><a data-tooltip="Raporu Göster" href="index.php?p=reports/ysc/report-view-ysc&id=<?= $list->report_id ?>" target="_blank"><?= $list->report_number ?></a></td>
                        <td><?= $list->cihaz_no?></td>
                        <td><?= $list->bulundugu_bolge?></td>
                        <td><?= $list->ay ?></td>
                        <td><?= $list->yil ?></td>
                    </tr>
              
            <?php $i++; 
                    endforeach; ?>
        </table>

    </div>
</div>
<script src="include/js/data-table.js"></script>


<script>
$(document).on('click', '#filling_list-toxls', function() {
    var month = $('#control_month').val();
    var year = $('#control_year').val();
    var url = '/pages/1/reports/ysc/filling-list-export-toxls.php?month=' + month + '&year=' + year;
    window.location.href = url;
});

</script>