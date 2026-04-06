<?php

use Illuminate\Database\Eloquent\Model;

class PvTransfers extends Model {
    protected $table = 'pv_transfers';
    protected $fillable = [
        'id',
        'voucher_id',
        'from_customer_id',
        'to_customer_id',
        'reference_id',
        'notes'
    ];
    
    public $timestamps = true;
    
}