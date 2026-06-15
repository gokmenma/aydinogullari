
<!-- Modal -->
<div class="modal fade" id="uploadfromxlsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Excelde Yükle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="col-12">
                        <label for="">Yüklenecek Dosya</label>
                        <input type="file" name="file_name" id="file_name" class="form-control btn-sm" accept=".xls,.xlsx" placeholder="Dosya seçiniz.Yalnızca xls,xlsx">
                        <p>Yüklenecek şablon dosyasını <strong><a href="pages/1/reports/hst/sablon.xlsx"><span class="text-danger">buradan</span>  </a> </strong> indirebilirsiniz</p>
                        <label class="badge badge-danger text-white p-2" for="" id="lblWarning"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Vazgeç</button>
                <button type="button" class="btn btn-primary" id="uploadFromXlsButton" data-dismiss="modal">Yükle</button>
            </div>
        </div>
    </div>
</div>