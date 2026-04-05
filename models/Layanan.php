<?php

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model {
    protected $table = 'tb_layanan';
    protected $fillable = [
        'id',
        'kode',
        'keterangan',
        'idParent',
        'icon',
        'deskripsi',
        'gambar',
        'release_status'
    ];
    
    public $timestamps = false;

    public function parent() {
        return $this->belongsTo(Layanan::class, 'idParent');
    }
    
}