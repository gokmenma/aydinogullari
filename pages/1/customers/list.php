<?php

if (@$_GET["id"] && @$_GET["mode"] == "delete" && @$_GET["code"] == "04md177") {
    permcontrol("customerdelete");
    $cdid = $_GET["id"];
    $contq = $ac->prepare("SELECT * FROM customers WHERE id = ?");
    $contq->execute(array($cdid));
    if ($contq->fetch(PDO::FETCH_ASSOC)) {
        $deletq = $ac->prepare("DELETE FROM customers WHERE id = ?");
        $deletq->execute(array($cdid));


        $deletqp = $ac->prepare("DELETE FROM projects WHERE pcid = ?");
        $deletqp->execute(array($cdid));

        $deletqo = $ac->prepare("DELETE FROM offers WHERE cid = ?");
        $deletqo->execute(array($cdid));


        if ($deletq) {
            header("Location: index.php?p=customers&id=$cdid&type=delete");
        }
    }
}

?>


<?php


?>
<div class="content pd-20 bg-white border-radius-16 box-shadow mb-30">
    <!-- Modal -->
    <div class="modal fade" id="customerdetails" tabindex="-1" role="dialog"
        aria-labelledby="customerdetailsCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerdeteailHeader"> Detay Bilgisi</h5>
                    <button type="button" class="closeModal close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   
                    <style>
                        table tr td{
                           padding: 5px;
                           
                        }
                    </style>
                    <table>
                        <tr>
                            <td><label for="">Kayıt Yapan Personel :</label></td>
                            <td><label for="" id="creator"></label></td>
                        </tr>
                        <tr>
                            <td><label for="">Kayıt Tarihi :</label></td>
                            <td><label for="" id="create_time"></label></td>
                        </tr>

                        <tr>
                            <td><label for="">Güncelleme Yapan Personel :</label></td>
                            <td><label for="" id="updater"></label></td>
                        </tr>
                        <tr>
                            <td><label for="">Güncelleme Tarihi :</label></td>
                            <td><label for="" id="updated_at"></label></td>
                        </tr>
                    </table>



                </div>
                <div class="modal-footer">

                    <button type="button" class="closeModal btn btn-primary" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->


    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Müşteri Listesi</h5>
            <p class="font-14"> </p>
        </div>
        <?php if (permtrue("customeradd")) { ?>
            <a href="index.php?p=customers/manage"><button type="button" class="btn btn-primary btn-sm float-right"><i
                        class="fa fa-plus"></i> Yeni
                    Müşteri</button></a>
        <?php } ?>
    </div>
    <table id="customerlist" class="data-table table-bordered table-hover table-sm table-responsive">
        <thead>
            <tr>
                <th scope="col" class="app-item-number">Sıra</th>
                <th>Firma Adı</th>
                <th>Grup</th>
                <th>Satış Temsilcisi</th>
                <th>Teklif/Servis Sayısı</th>
                <th>E-Posta Adresi</th>
                <th>GSM</th>
                <th class="datatable-nosort" style="min-width:90px">İşlem</th>

            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script src="include/js/data-table.js"></script>
<script>
    $(document).ready(function () {
        $('#customerlist').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'api/customers_datatables.php',
                type: 'GET'
            },
            columns: [
                { data: 0, className: 'text-center' },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4, orderable: false },
                { data: 5 },
                { data: 6 },
                { data: 7, orderable: false }
            ],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                url: 'include/js/tr.json',
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Yükleniyor...</span>'
            },
            responsive: true,
            order: [[0, 'desc']],
            orderCellsTop: true,
            initComplete: function () {
                var api = this.api();
                var tableId = api.table().node().id;
                // Arama satırını <thead> içine ekle
                $("#" + tableId + " thead").append('<tr class="search-input-row"></tr>');

                api.columns().every(function (index) {
                    let column = this;
                    let header = $(column.header());
                    let title = header.text();

                    // Sadece arama yapılabilecek alanlar için input oluştur
                    if (column.visible() && title && title.trim() !== 'İşlem' && title.trim() !== 'İşlemler' && title.trim() !== 'Sıra' && title.trim() !== 'Teklif/Servis Sayısı') {
                        let input = $('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" autocomplete="off">')
                            .appendTo($('<th class="search"></th>').appendTo("#" + tableId + " .search-input-row"))
                            .on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                    } else {
                        $("#" + tableId + " .search-input-row").append('<th></th>');
                    }
                });
            }
        });

        // Detay butonu için event delegation kullan
        $(document).on("click", ".btn-detail", function () {
            var id = $(this).data("id");
            $.ajax({
                method: "POST",
                url: "pages/1/ajax.php?type=customer-detail",
                dataType: "json",
                data: {
                    id: id
                },
                success: function (response) {
                    $("#customerdetails").modal("show");
                    $("#creator").text(response.creator);
                    $("#create_time").text(response.create_time);
                    $("#updater").text(response.updater);
                    $("#updated_at").text(response.updated_at);
                }
            });
        });
    });

    $(".closeModal").click(function () {
        $("#customerdetails").modal("hide");
    });
</script>