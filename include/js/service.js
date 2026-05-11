    $(document).ready(function () {
       
        $('#company').change(function () {
            var selectedCompanyId = $(this).val();
            $.ajax({
                url: "pages/1/ajax.php",
                type: "POST",
                dataType: 'json',
                data: {
                    customer_id: selectedCompanyId,
                },
                success: function (data) {

                    $('#address').val(data.city + " / " + data.ilce);
                    $('#region').val(data.region);

                    var offers = data.offers;
                    var options = '';
                   
                    if (data.offers.length > 0) {
                        options += '<option value="">Onaylanmış Teklif Seçin</option>';
                        $.each(offers, function (index, offer) {
                            options += '<option value="' + offer.id + '">' + offer
                                .offerNumber + '</option>';
                        });
                        $('#offerno').html(options).show();
                        $('#offerno').selectpicker("refresh");

                    } else {
                        options += '<option value="">Teklif No Yok</option>';
                       
                    }
                    $('#offerno').html(options).show();
                    $('#offerno').selectpicker("refresh");
                },
                error: function (xhr, status, error) {
                    console.error(error);                 
                }

            })
        });

    });
