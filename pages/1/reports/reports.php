<?php
$ris = $_GET["id"] ?? null;
if (($_GET["st"] ?? "") == "success-mail") {
	showAlert("success", "Mail başarı ile gönderildi!");
}

?>


<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">

    <!-- Modal -->
    <div class="modal fade" id="reportdetail" tabindex="-1" role="dialog" aria-labelledby="reportdetailCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportdetailLongTitle"> Detay Bilgisi</h5>
                    <button type="button" class="closeModal close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row ml-2 mt-4">
                        Kayıt Yapan Personel : <label for="" id="creator"></label>
                    </div>
                    <div class="row ml-2 mb-4">
                        Kayıt Tarihi : <label for="" id="create_time"></label>
                    </div>



                </div>
                <div class="modal-footer">

                    <button type="button" class="closeModal btn btn-primary" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->



    <div class="clearfix mb-30">
        <div class="pull-left">
            <h5 class="text-blue">Rapor Listesi</h5>
            <p class="font-14"> </p>
        </div>
        <div class="float-right mb-20">
            <a href="#" class="btn btn-sm btn-primary" id="report-new" data-toggle="modal" data-type="new"
                data-target="#reporttypeModal"><i class="fa fa-plus"></i> Yeni Oluştur</a>
            <a href="#" id="content-view" class="btn btn-sm btn-success" data-type="content" data-toggle="modal"
                data-target="#reporttypeModal"><i class="fa fa-folder"></i>
                İçerik Listesi</a>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="reporttypeModal" tabindex="-1" role="dialog" aria-labelledby="reporttypeModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reporttypeModalLongTitle">Rapor Türü Seçiniz</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <select name="reporttype" id="reporttype" class="form-control selectpicker"
                            data-style="bg-white border">
                            <?php
                            $sql = $ac->prepare("SELECT * FROM report_types ");
                            $sql->execute();

                            while ($type = $sql->fetch(PDO::FETCH_ASSOC)) {
                                $newpagelink = "reports/" . $type["page_link"] . "/report-new-" . $type["page_link"];
                                $content_pagelink = "reports/" . $type["page_link"] . "/report-content-" . $type["page_link"];
                                ?>

                                <option value="<?php echo $type["id"] ?>" data-new="<?php echo $newpagelink ?>"
                                    data-view="<?php echo $content_pagelink ?>">
                                   
                                    <?php echo $type["reportName"] ?>
                                </option>

                            <?php } ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="button" id="forwardtoreport" data-type="" class="btn btn-primary">Devam
                            Et</button>
                    </div>
                </div>
            </div>
        </div>



<div class="table-responsive">


        <table id="reportTable" class="data-table table-hover table-bordered text-nowrap">
            <thead>
                <tr>

                    <th class="w-10 text-nowrap">ID</th>
                    <th class="w-10 text-nowrap">Rapor No</th>
                    <th>Firma</th>
                    <th>Rapor Türü</th>
                    <th>İş Emri No</th>
                    <th>Kontrol Tarihi</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>İşlem</th>

                </tr>
            </thead>
            <tbody>
                <?php

                $query = $ac->prepare("SELECT r.id,r.report_number,
                                                rt.reportName,rt.page_link,
                                                r.isemrino,r.control_date,
                                                r.validity_date,
                                                c.company FROM `reports` r 
                                                LEFT JOIN report_types rt on rt.id = r.report_type  
                                                LEFT JOIN customers c on c.id = r.customer_id ORDER BY r.id desc
                                                ");
                $query->execute(array());

                $sirano = 1;
                while ($reports = $query->fetch(PDO::FETCH_ASSOC)) {

                    $newpagelink = "index.php?p=reports/" . $reports["page_link"] . "/report-new-" . $reports["page_link"];
                    $edit_file = ($reports["page_link"] == "yas") ? "report-new-" : "report-edit-";
                    $editpagelink = "index.php?p=reports/" . $reports["page_link"] . "/" . $edit_file . $reports["page_link"] . "&id=" . $reports["id"];
                    $viewpagelink = "index.php?p=reports/" . $reports["page_link"] . "/report-view-" . $reports["page_link"] . "&id=" . $reports["id"];
                    $send_mail_link = "index.php?p=report-send-as-mail&type=".$reports['page_link']."&id=". $reports["id"];
                    
                    ?>
                    <tr>
                        <!-- ID No -->
                        <td class="text-center">
                            <?php echo $reports["id"]; ?>
                        </td>
                        <!-- Sıra No -->
                        <td class="text-center">
                            <?php echo $reports["report_number"]; ?>
                        </td>

                        <td class="table-plus " data-tooltip="<?php echo $reports["company"]; ?>">
                            <?php echo shorted($reports["company"],40); ?>
                        </td>

                        <!-- Rapor Türü -->
                        <td class="table-plus ">
                            <?php
                            echo $reports["reportName"];

                            ?>
                        </td>

                        <!-- is Emri No -->
                        <td class="table-plus ">
                            <?php echo $reports["isemrino"]; ?>
                        </td>

                        <!-- Geçerlilik Tarihi -->
                        <td class="table-plus ">
                            <?php echo $reports["control_date"]; ?>
                        </td>

                        <!-- Firma -->
                        <td class="table-plus ">
                            <?php echo $reports["validity_date"]; ?>

                        </td>

                      

                        <td class="text-center app-item-action-3">

                            <?php


                            if (permtrue("reportedit")) { ?>
                                <a type="button" href="<?php echo $editpagelink ?>" class="btn btn-sm btn-outline-primary"
                                    data-tooltip="Düzenle"><i class="fa fa-pencil"></i></a>

                            <?php }
                            if (permtrue("reportdel")) { ?>
                                <button type="button" class="btn btn-sm btn-danger" data-tooltip="Sil"
                                    onClick="deleteRecord('<?php echo $reports["report_number"] ?> nolu raporu silmek istediğinize emin misiniz?','<?php echo $reports["id"]; ?>','reports/reports','reports')"><i
                                        class="fa fa-trash"></i></button>

                            <?php } ?>
                            

                            <div class="dropdown d-inline">
                                <button class="btn btn-secondary btn-sm" type="button" id="dropdownMenu2"
                                    data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v ml-1 mr-1"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-detail"
                                    aria-labelledby="dropdownMenu2">


                                    <?php if (permtrue("offerview")) { ?>
                                        <a href="<?php echo $viewpagelink ?>" target="_blank" class="dropdown-item"
                                            type="button">
                                            <i class="fa fa-file mr-2"></i>
                                            Raporu Göster</a>
                                        <a href="<?php echo $viewpagelink . '&sign=no'?>" target="_blank" class="dropdown-item"
                                            type="button">
                                            <i class="fa fa-file mr-2"></i>
                                            İmzasız Raporu Göster</a>

                                    <?php }
                                    ?>
                                       <a href="<?php echo $send_mail_link ?>" target="_blank" class="dropdown-item"
                                            type="button">
                                            <i class="fa fa-file mr-2"></i>
                                             Mail gönder</a>
                                  

                                    <a class="btn-report-detail btn dropdown-item" data-id="<?php echo $reports["id"]; ?>"
                                        type="button">
                                        <i class="fa fa-copy mr-2"></i>
                                        Detay Bilgisi</a>

                                </div>

                            </div>


                        </td>

                    <?php } ?>

                </tr>


            </tbody>
            <tfoot>
                <tr>
                    <th class="w-10 text-nowrap">ID</th>
                    <th class="w-10 text-nowrap">Rapor No</th>
                    <th>Firma</th>
                    <th>Rapor Türü</th>
                    <th>İş Emri No</th>
                    <th>Kontrol Tarihi</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>İşlem</th>

                </tr>
            </tfoot>
        </table>

        </div>
    </div>
</div>

<script src="include/js/data-table.js"></script>
<script src="include/js/report.js"></script>
<script>
    $(document).ready(function () {
        $(".btn-report-detail").click(function () {
            var id = $(this).data("id");
            $.ajax({
                method: "POST",
                url: "pages/1/ajax.php?type=report-detail",
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $("#reportdetail").modal("show");
                    $("#creator").text(data.creator);
                    $("#create_time").text(data.create_time);

                }
            })

        });

    });
</script>