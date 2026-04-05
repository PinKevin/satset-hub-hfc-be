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
}