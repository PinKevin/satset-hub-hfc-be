<?php

use Illuminate\Database\Eloquent\Model;

class PvRedemptions extends Model {
    protected $table = 'pv_redemptions';
    protected $fillable = [
        'id',
        'voucher_id',
        'user_id',
        'id_layanan',
        'redeemed_value'
    ];
    
    public $timestamps = true;
    
}