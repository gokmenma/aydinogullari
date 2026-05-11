<tr tabindex="<?php echo $tabindex ;?>"> 
    <td class="pl-2">
        <button class="sil btn btn-sm btn-danger"> Sil</button>
    </td>
    <td><input required type="text" class="form-control region" id="testno" name="testno[]" value="<?php echo $testno ?>"></td>
    <td>
        <input type="text" class="form-control region" name="kg[]" value="<?php echo $kg ?>">
    </td>
    <td>
        <input type="text" required autocomplete="off" class="form-control region" name="cinsi[]" value="<?php echo $cinsi; ?>">
    </td>
    <td>
        <input type="text" required autocomplete="off" class="form-control region" name="imalatci_firma[]"
            value="<?php echo $imalatci_firma; ?>">
    </td>
    <td>
        <input type="text" required autocomplete="off" class="form-control imal" name="imal_tarihi[]" value="<?php echo $imal_tarihi; ?>">
    </td>
    <td>
        <input type="text" required autocomplete="off" class="form-control region" name="serino[]" value="<?php echo $serino; ?>">
    </td>
    <td>
        <select required name="tse_belgesi[]" class="form-control region" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $tse_belgesi == "0" ? " selected" : "" ;?> value="0">YOK</option>
            <option <?php echo $tse_belgesi == "1" ? " selected" : "" ;?> value="1">VAR</option>
        </select>
    </td>
    <td>
        <select required name="yuzey_durumu[]" class="form-control region" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $yuzey_durumu == "1" ? " selected" : "" ;?> value="1">OLUMLU</option>
            <option <?php echo $yuzey_durumu == "0" ? " selected" : "" ;?> value="0">OLUMSUZ</option>
        </select>
    </td>

    <td>
        <select required name="sizdirmazlik_deneyi[]" class="form-control" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $sizdirmazlik_deneyi == "0" ? " selected" : "" ;?> value="0">YOK</option>
            <option <?php echo $sizdirmazlik_deneyi == "1" ? " selected" : "" ;?> value="1">VAR</option>
        </select>
    </td>
    <td>
        <select required name="esneme_deneyi[]" class="form-control" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $esneme_deneyi == "1" ? " selected" : "" ;?> value="1">OLUMLU</option>
            <option <?php echo $esneme_deneyi == "0" ? " selected" : "" ;?> value="0">OLUMSUZ</option>
        </select>
    </td>

    <td>
    <textarea type="text" autocomplete="off" class="form-control things" style="height:40px;resize:both" name="things[]"
            value="<?php echo $notes; ?>"><?php echo $things ?></textarea>
    </td>
     
</tr> 

<style>
    .imal{
        width: 100px;
    }
</style>