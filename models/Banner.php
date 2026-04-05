<?php

use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $table = 'banners';
    protected $fillable = [
        'id',
        'judul',
        'deskripsi',
        'gambar',
        'button_text',
        'button_link',
        'button_color',
        'target',
        'is_active',
        'urutan',
        'mulai_tampil',
        'selesai_tampil'
        
    ];
    
    public $timestamps = true;

}