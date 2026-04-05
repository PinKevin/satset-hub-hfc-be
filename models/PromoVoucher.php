<?php

use Illuminate\Database\Eloquent\Model;

class PromoVoucher extends Model {
    protected $table = 'promo_vouchers';
    protected $fillable = [
        'id',
        'program_name',
        'program_description',
        'voucher_code_prefix',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'idLayanan',
        'valid_from',
        'valid_until',
        'usage_limit_per_customer',
        'total_voucher',
        'used_voucher',
        'template_design',
        'term_conditions',
        'created_by',
        'is_active',
        'start_date',
        'end_date',
        'target_audience',
        'distribution_method',
        'whatsapp_template',
        'print_layout',
    ];
    
    public $timestamps = true;

    public function layanan() {
        return $this->belongsTo(Layanan::class, 'idLayanan');
    }

    public function isValid() {
        $now = date('Y-m-d H:i:s');
        return $this->is_active && $this->valid_from <= $now && $this->valid_until >= $now;
    }

    public function canBeUsedByCustomer($customerId) {
        if (!$this->isValid()) {
            return false;
        }

        $usageCount = PromoVoucherUsage::where('promo_voucher_id', $this->id)->where('customer_id', $customerId)->count();

        return $usageCount < $this->usage_limit_per_customer;
    }

}