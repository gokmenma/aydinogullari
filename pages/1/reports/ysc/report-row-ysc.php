<tr tabindex="<?php echo $tabindex ;?>"> 
    <td class="pl-2">
        <button class="sil btn btn-sm btn-danger"> Sil</button>
    </td>
    <td><input required type="text" class="form-control satir_no" id="cihazno" name="cihazno[]" value="<?php echo $cihaz_no ?? 1 ?>"></td>
    <td>
        <input type="text" class="form-control region" name="cihazbolge[]" value="<?php echo $cihazbolge ?>">
    </td>
    <td>
        <input required type="text" class="form-control region" name="cinsi[]" value="<?php echo $cinsi ?>">
    </td>
    <td data-tooltip="aa/yyyy veya aa-yyyy veya aa.yyyy şeklinde girebilirsiniz">
        <input type="text" autocomplete="off" style="min-width:120px" class="form-control filling-date" placeholder="aa/yyyy" name="dolumtarihi[]" value="<?php echo $dolumtarihi; ?>">
    </td>
    <td>
        <input type="text" autocomplete="off" style="min-width:120px" class="form-control expiration-date " placeholder="aa/yyyy" name="sonkullanimtarihi[]" value="<?php echo $sonkullanimtarihi; ?>">
    </td>
    <td>

        <input type="text" autocomplete="off" class="form-control date-input" name="kontoltarihi1[]" value="<?php echo $kontoltarihi1; ?>">
    </td>
    <td>
        <input type="text" autocomplete="off" class="form-control date-input" name="kontoltarihi2[]" value="<?php echo $kontoltarihi2; ?>">
    </td>
    <td style="min-width:170px">
        <div class="input-group m-0 p-0">
            <input name="islemkontroltarihi1[]" value="<?php echo $islemkontroltarihi1; ?>" type="text" class="form-control islemkontroltarihi" aria-label="Text input with dropdown button">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item btn">Basınç</a>
                    <a class="dropdown-item btn">Kontrol</a>
                    <a class="dropdown-item btn">Dolum</a>
                </div>
            </div>
        </div>

    </td>
    <td style="min-width:170px" class="d-flex">

        <div class="input-group m-0 p-0">
            <input name="islemkontroltarihi2[]" value="<?php echo $islemkontroltarihi2; ?>" type="text" class="form-control islemkontroltarihi" aria-label="Text input with dropdown button">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item btn">Basınç</a>
                    <a class="dropdown-item btn">Kontrol</a>
                    <a class="dropdown-item btn">Dolum</a>
                </div>
            </div>
        </div>

    </td>
    
    <td>
        <select name="dismuhafaza[]" class="form-control w-auto" style="min-width:auto">
            <option value="">Seçiniz</option>
            <option <?php echo $dismuhafaza == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $dismuhafaza == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="cevrekontrolu[]" class="form-control w-auto">
            <option value="">Seçiniz</option>
            <option <?php echo $cevrekontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $cevrekontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="pimkontrolu[]" class="form-control w-auto" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $pimkontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $pimkontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="manometrekontrolu[]" class="form-control w-auto" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $manometrekontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $manometrekontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="hortumkontrolu[]" class="form-control w-auto" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $hortumkontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $hortumkontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="talimatkontrolu[]" class="form-control w-auto" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $talimatkontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $talimatkontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>
    <td>
        <select name="agirlikkontrolu[]" class="form-control w-auto" data-style="bg-white border">
            <option value="">Seçiniz</option>
            <option <?php echo $agirlikkontrolu == "0" ? " selected" : "" ?> value="0">UYGUN DEĞİL</option>
            <option <?php echo $agirlikkontrolu == "1" ? " selected" : "" ?> value="1">UYGUN</option>
        </select>
    </td>

</tr>