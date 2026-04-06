<?php

use Illuminate\Database\Eloquent\Model;

class PvBatches extends Model {
    protected $table = 'pv_batches';
    protected $fillable = [
        'id',
        'batch_name',
        'voucher_prefix',
        'id_layanan',
        'face_value',
        'selling_price',
        'total_qty',
        'sold_qty',
        'valid_from',
        'valid_until',
        'voucher_name',
        'voucher_description',
        'voucher_icon',
        'voucher_image',
        'template_image',
        'code_pos_x',
        'code_pos_y',
        'code_font_size',
        'code_color',
        'code_rotation',
        'template_layout',
        'output_format',
        'is_active',
        'created_by'
    ];
    
    public $timestamps = true;
    
}