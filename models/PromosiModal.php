<?php

use Illuminate\Database\Eloquent\Model;

class PromosiModal extends Model {
    protected $table = 'promosi_modals';
    protected $fillable = [
        'id',
        'judul',
        'konten',
        'gambar',
        'tipe',
        'primary_button_text',
        'primary_button_link',
        'primary_button_color',
        'secondary_button_text',
        'secondary_button_link',
        'secondary_button_color',
        'show_close_button',
        'auto_close_delay',
        'is_active',
        'mulai_tampil',
        'selesai_tampil',
    ];
    
    public $timestamps = true;

}