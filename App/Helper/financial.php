<?php 

namespace App\Helper;

class Financial {
    //PARA BİRİMLERİ
    const PARABİRİMİ = [
        'TRY' => 'TRY',
        'USD' => 'USD',
        'EUR' => 'EUR'
    ];

    //Para birimi seçimi
    public static function getCurrencySelect($name = 'currency', $id = null)
    {
        $select = '<select name="' . $name . '" id="' . $name . '" class="selectpicker form-control" data-style="border bg-white">';
        foreach (self::PARABİRİMİ as $key => $value) {
            $selected = $key == $id ? 'selected' : null;
            $select .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $select .= '</select>';

        return $select;
    }


    
    public static function formattedMoneyToNumber($value)
    {
        return str_replace(['₺', '.', ','], ['', '', '.'], $value);
    }
    public static function formattedMoney($value, $currency = 1)
    {
        // Sayıyı yuvarla ve iki ondalık basamağa kadar biçimlendir
        $value = number_format($value, 2, ',', '.');
    
        // Sayıyı doğru şekilde biçimlendir
        $parts = explode(',', $value);
        $parts[0] = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $parts[0]);
    
        return implode(',', $parts);
    }
}

