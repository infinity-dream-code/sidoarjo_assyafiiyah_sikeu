<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scctbill extends Model
{
    protected $connection = "DATA_MYSQL";

    protected $table = "scctbill";

    protected $primaryKey = "AA";

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        "CUSTID",
        "BILLCD",
        "BILLAC",
        "BILLNM",
        "BILLAM",
        "BILLPAID",
        "PAYMENTLEFT",
        "FLPART",
        "PAIDST",
        "PAIDDT",
        "PAIDDT_ACTUAL",
        "NOREFF",
        "FSTSBolehBayar",
        "FUrutan",
        "FTGLTagihan",
        "FIDBANK",
        "FRecID",
        "AA",
        "BTA",
        "BILLTOT",
        "TRANSNO",
        "BAYAR",
        "INSTALLMENT",
        "isINSTALLABLE",
    ];

    public array $metodeBayar = [
        "1140000" => "Manual Tunai",
        "1140001" => "Manual BMI",
        "1140002" => "Manual Saldo",
        "1140003" => "Transfer Bank Lain",
        "1140004" => "INFAQ",
        "1140005" => "Transfer Bank BRI",
        "1200001" => "Loket Manual - Beasiswa",
        "1200002" => "Loket Manual - Potongan",
        "1" => "H2H VA BMI - ATM",
        "2" => "H2H VA BMI - Teller",
        "3" => "H2H VA BMI - IBANK",
        "4" => "H2H VA BMI - EDC",
        "5" => "H2H VA BMI - MOBILE",
        "6" => "ANDROID",
        null => "Nomor VA",
        "" => "Nomor VA",
    ];
}
