<?php

use Illuminate\Database\Eloquent\Model;

class PvVouchers extends Model {
    protected $table = 'pv_vouchers';
    protected $fillable = [
        'id',
        'batch_id',
        'voucher_code',
        'face_value',
        'status',
        'current_owner_id',
        'original_owner_id',
        'sold_at',
        'used_at',
        'rendered_image',
    ];
    
    public $timestamps = true;
    
}