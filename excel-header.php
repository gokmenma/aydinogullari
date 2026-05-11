<?php 

echo '<meta charset="utf-8" />'; //HER İHTİMALE KARŞI KARAKTER HATASI ALMAMASI İÇİN HTML UTF-8 KONTROLÜNÜ EKLEDİK.
header("Content-Type: application/xls; charset=utf-8"); //HANGİ DOSYA İŞLEMİNİ YAPACAĞINI VE KARAKTERİNİ BELİRLEDİK.
header("Content-Disposition: attachment; filename=$filename.xls"); //İNDİRİLECEK OLAN DOSYANIN ADINI VE UZANTISINI BELİRLEDİK.
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); //ÖN BELLEK KONTROLÜ; CHECK ALANLARINI 0 YAPARAK ÖN BELLEK KONTROLLERİNİ KAPATTIK
header("Cache-Control: private", false); //ÖN BELLEK KONTROLÜ; FALSE YAPARAK ÖN BELLEĞİ KALDIRDIK.
// Add UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";
?>