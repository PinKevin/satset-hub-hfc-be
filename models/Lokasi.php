<?php

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'tb_lokasi';
    public $timestamps = false;
    protected $primaryKey = 'Id';
    protected $fillable = [
        'NamaLokasi',
        'alamat',
        'RT',
        'RW',
        'idProvince',
        'idRegencies',
        'idCustomer',
        'idDistricts',
        'idVillages',
        'namaPIC',
        'noHpPIC',
        'emailPIC',
        'keterangan',
        'jenisBangunan',
        'jenisLayanan',
        'maps',
        'status'
    ];

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'idProvince');
    }

    public function regency()
    {
        return $this->belongsTo(Regencies::class, 'idRegencies');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'idDistricts');
    }

    public function village()
    {
        return $this->belongsTo(Villages::class, 'idVillages');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'idCustomer');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'jenisLayanan');
    }

}
