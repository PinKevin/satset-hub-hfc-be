<?php

class PromoVoucherController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        try {
            $promoVouchers = PromoVoucher::all();
            return $this->success($promoVouchers);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch promo vouchers: ' . $e->getMessage());
        }
    }

    public function show($id) {
        $this->auth->authenticate();
        try {
            $promoVoucher = PromoVoucher::find($id);
            if (!$promoVoucher) {
                return $this->notFound('Promo voucher not found');
            }
            return $this->success($promoVoucher);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch promo voucher: ' . $e->getMessage());
        }
    }

    public function redeem() {
        $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, ['promoVoucherId']);
        if ($validation) return $validation;
        try {
            $promoVoucher = PromoVoucher::find($data['promoVoucherId']);
            if (!$promoVoucher) {
                return $this->notFound('Promo voucher not found');
            }
            
            if ($promoVoucher->is_redeemed) {
                return $this->badRequest('Promo voucher already redeemed');
            }
            
            $promoVoucher->is_redeemed = true;
            $promoVoucher->redeemed_at = date('Y-m-d H:i:s');
            $promoVoucher->save();
            
            return $this->success(null, 'Promo voucher redeemed successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to redeem promo voucher: ' . $e->getMessage());
        }
    }

    public function create() {
        $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, ['code', 'discount']);
        if ($validation) return $validation;
        
        try {
            $promoVoucher = new PromoVoucher();
            $promoVoucher->code = $data['code'];
            $promoVoucher->discount = $data['discount'];
            $promoVoucher->is_redeemed = false;
            $promoVoucher->save();
            
            return $this->created($promoVoucher, 'Promo voucher created successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to create promo voucher: ' . $e->getMessage());
        }
    }
}