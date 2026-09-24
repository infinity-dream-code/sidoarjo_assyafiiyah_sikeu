<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodeBayar extends Model
{
    public static function attributes(): array
    {
        return [
            '1140000' => 'Manual Tunai',
            '1140001' => 'Manual BMI',
            '1140002' => 'Manual Saldo',
            '1140003' => 'Transfer Bank Lain',
            '1140004' => 'Transfer Bank BNI',
            '1140005' => 'Transfer Bank BRI',
            '1200001' => 'Loket Manual - Beasiswa',
            '1200002' => 'Loket Manual - Potongan',
            '1' => 'H2H VA BMI - ATM',
            '2' => 'H2H VA BMI - Teller',
            '3' => 'H2H VA BMI - IBANK',
            '4' => 'H2H VA BMI - EDC',
            '5' => 'H2H VA BMI - MOBILE',
            '6' => 'ALL BMI',
//            "NULL" => 'Nomor VA', //NULL not working on default filter logic
//            'empty' => 'Nomor VA' //'' not working on default filter logic
        ];
    }
}
