<?php

class BannerController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        try {
            $banners = Banner::all();
            return $this->success($banners);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch banners: ' . $e->getMessage());
        }
    }
}