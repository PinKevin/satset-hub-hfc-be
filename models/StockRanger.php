<?php

use Illuminate\Database\Eloquent\Model;

class StockRanger extends Model
{
    protected $table = 'tb_stock_ranger';
    public $timestamps = false;
    protected $fillable = ['idRanger', 'tgl', 'status'];

    public function ranger()
    {
        return $this->belongsTo(Karyawan::class, 'idRanger');
    }
}
