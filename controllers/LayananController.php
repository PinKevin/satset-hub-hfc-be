<?php

class LayananController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        try {
            $layanans = Layanan::all()->toArray(); 

            $map = [];
            $result = [];

            foreach ($layanans as $item) {
                $item['children'] = [];
                $map[$item['id']] = $item;
            }

            foreach ($map as $id => &$item) {
                if ($item['idParent'] == 0) {
                    $result[] = &$item;
                } else {
                    if (isset($map[$item['idParent']])) {
                        $map[$item['idParent']]['children'][] = &$item;
                    }
                }
            }

            return $this->success($result);

        } catch (Exception $e) {
            return $this->serverError('Failed to fetch layanans: ' . $e->getMessage());
        }
    }
}