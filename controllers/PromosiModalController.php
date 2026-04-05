<?php

class PromosiModalController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        try {
            $promosiModals = PromosiModal::all();
            return $this->success($promosiModals);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch promosi modals: ' . $e->getMessage());
        }
    }
}