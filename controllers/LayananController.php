<?php

class LayananController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        try {
            $layanans = Layanan::all()->toArray(); 

            $map = [];
            $result = [];

            foreach ($layanans as $item) {
                $map[$item['id']] = [
                    'id' => $item['id'],
                    'idParent' => $item['idParent'],
                    'kode' => $item['kode'],
                    'keterangan' => $item['keterangan'],
                    'icon' => $item['icon'],
                    'harga' => $item['harga'],
                    'thumbnail' => $item['thumbnail'],
                    'release_status' => $item['release_status'],
                    'children' => []
                ];
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

    public function show($id) {
        // $this->auth->authenticate();
        try {
            $layanan = Layanan::find($id);
            
            if (!$layanan) {
                return $this->notFound('Layanan not found');
            }
            
            return $this->success([
                'id' => $layanan->id,
                'idParent' => $layanan->idParent,
                'kode' => $layanan->kode,
                'keterangan' => $layanan->keterangan,
                'icon' => $layanan->icon,
                'harga' => $layanan->harga,
                'thumbnail' => $layanan->thumbnail,
                'gambar' => $layanan['gambar'],
                'deskripsi' => $layanan['deskripsi'],
            ]);
            
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch layanan: ' . $e->getMessage());
        }
    }
}