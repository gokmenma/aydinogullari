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


        <table id="reportTable" class="data-table table-hover table-bordered text-nowrap" style="width: 100%;">
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
            </tbody>
        </table>

        </div>
    </div>
</div>

<script src="include/js/data-table.js"></script>
<script src="include/js/report.js"></script>
<script>
    $(document).ready(function () {
        if ($("#reportTable").length) {
            $("#reportTable").DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                autoWidth: false,
                ajax: {
                    url: "api/reports_datatables.php",
                    type: "GET"
                },
                columns: [
                    { data: 0, className: "text-center" }, // ID
                    { data: 1, className: "text-center" }, // Rapor No
                    { data: 2 }, // Firma
                    { data: 3 }, // Rapor Türü
                    { data: 4 }, // İş Emri No
                    { data: 5 }, // Kontrol Tarihi
                    { data: 6 }, // Geçerlilik Tarihi
                    { data: 7, orderable: false, className: "text-center" } // İşlem
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    url: "include/js/tr.json",
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Yükleniyor...</span>'
                },
                responsive: true,
                order: [[0, "desc"]],
                orderCellsTop: true,
                initComplete: function () {
                    var api = this.api();
                    var tableId = api.table().node().id;
                    $("#" + tableId + " thead").append('<tr class="search-input-row"></tr>');

                    api.columns().every(function (index) {
                        let column = this;
                        let header = $(column.header());
                        let title = header.text();

                        // Sadece arama yapılabilecek alanlar için input oluştur (İşlem hariç)
                        if (column.visible() && title && title.trim() !== "İşlem" && title.trim() !== "İşlemler") {
                            let input = $('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" autocomplete="off">')
                                .appendTo($('<th class="search"></th>').appendTo("#" + tableId + " .search-input-row"))
                                .on("keyup change clear", function () {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        } else {
                            $("#" + tableId + " .search-input-row").append("<th></th>");
                        }
                    });

                    // Restore state if stateSave is enabled
                    var state = api.state.loaded();
                    if (state) {
                        $("#" + tableId + " .search-input-row input").each(function () {
                            var colIdx = $(this).parent().index();
                            var searchValue = state.columns[colIdx].search.search;
                            if (searchValue) {
                                $(this).val(searchValue);
                            }
                        });
                    }
                }
            });
        }

        $(document).on("click", ".btn-report-detail", function () {
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
            });
        });
    });
</script>