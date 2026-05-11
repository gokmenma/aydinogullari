

function updateToplamPurchase() {
    var alisToplam = 0;
    var DolarToplam = 0;
    var EuroToplam = 0;
    var TLToplam = 0;
    var TotalTL = 0;
    var total = 0;
  
    var curDollar = $("#cur-Dollar").val().replace(",", ".");
    var curEuro = $("#cur-Euro").val().replace(",", ".");
  
    // Her bir satır için birim fiyat ve adet bilgilerini alarak toplamı hesapla
    $("#tProduct tbody tr").each(function () {
      //Birim Fiyatı
  
      var alisFiyat = parseFloat($(this).find("[id^='buyprice']").val());
      //Miktarı
      var amount = parseFloat($(this).find("[id^='amount']").val());
      //Para Birimi
      var buycurType = $(this).find("[id^='buycur']").val();
  
      if (!isNaN(alisFiyat)) {
        //Para Birimi Dolar ise
        if (buycurType === "USD") {
          alisToplam = curDollar * alisFiyat * amount;
          total = alisFiyat * amount * curDollar;
  
          //Alttoplamdaki Dolar Toplamı hesaplanır
          DolarToplam = DolarToplam + alisFiyat * amount;
        } else if (buycurType === "EUR") {
          alisToplam = curEuro * alisFiyat * amount;
          total = alisFiyat * amount * curEuro;
  
          //Alttoplamdaki Euro Toplamı hesaplanır
          EuroToplam = EuroToplam + alisFiyat * amount;
        } else if (buycurType === "TRY") {
          alisToplam = alisFiyat * amount;
          total = alisFiyat * amount;
  
          //Alttoplamdaki TL Toplamı hesaplanır
          TLToplam = TLToplam + alisFiyat * amount;
        }
      }
    });
    //Toplam TL Karşılığı yazdırılır
    TotalTL = (DolarToplam * curDollar) + (EuroToplam * curEuro) + TLToplam;
    // Hesaplanan toplamı AltToplam etiketine yaz
    $(".AltToplam").text(formatNumber(alisToplam));
    $("#buy-tl").text(formatNumber(alisToplam));
    $("#discount").text($("#iskonto").val());
    $("#kdv-rate").text($("#Kdv").val());
    


    console.log(kdvTutari())
    //Kdv eklenip iskonto oranı düşüldükten sonra kalan miktar yazdırılır
    if (!isNaN(TotalTL)) {
      $("#altToplamInput").val(kdvHesapla(TotalTL));
      
      $("#lblTotalTL").text(kdvHesapla(TotalTL));
      $("#sonToplamFiyat").val(kdvHesapla(TotalTL));
    } else {
      $("#altToplamInput").val("0,00");
    }
    
    
  
    //Dolar cinsinden olan satırların toplam tutar hesaplanır
    $("#DolarAlttoplam").val(formatNumber(DolarToplam));
  
    //Euro cinsinden olan satırların toplam tutar hesaplanır
    $("#EuroAlttoplam").val(formatNumber(EuroToplam));
  
    //TL cinsinden olan satırların toplam tutar hesaplanır
    $("#TLAlttoplam").val(formatNumber(TLToplam));
  }