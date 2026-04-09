<?php

use Illuminate\Database\Eloquent\Model;

class Ranger extends Model
{
    protected $table = 'tb_ranger';
    public $timestamps = false;
    protected $fillable = [
        'idMitra', 'tgl', 'fotoUrl', 'fotoUrl2', 'jamMasuk',
        'jamKeluar', 'lat', 'lng', 'lat2', 'lng2', 'idOrder', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'idOrder');
    }
}
