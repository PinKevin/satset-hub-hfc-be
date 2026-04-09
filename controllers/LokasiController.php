<?php

class LokasiController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, [
            'user_id',
        ]);
        if ($validation) return $validation;
        $user_id = $data['user_id'];
        if (!$user_id) {
            return $this->validationError('user_id is required');
        }

        try {
            $lokasi = Lokasi::where('idCustomer', $user_id)->get();
            return $this->success($lokasi, 'Lokasi fetched successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user orders: ' . $e->getMessage());
        }
    }

    public function create() {
        $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, [
            'NamaLokasi',
            'alamat',
            'RT',
            'RW',
            'idProvince',
            'idRegencies',
            'idCustomer',
            'idDistricts',
            'idVillages',
            'namaPIC',
            'noHpPIC',
            'jenisBangunan',
        ]);

        if ($validation) return $validation;

        $NamaLokasi = $data['NamaLokasi'];
        $alamat = $data['alamat'];
        $RT = $data['RT'];
        $RW = $data['RW'];
        $idProvince = $data['idProvince'];
        $idRegencies = $data['idRegencies'];
        $idCustomer = $data['idCustomer'];
        $idDistricts = $data['idDistricts'];
        $idVillages = $data['idVillages'];
        $namaPIC = $data['namaPIC'];
        $noHpPIC = $data['noHpPIC'];
        $emailPIC = $data['emailPIC'] ?? null;
        $keterangan = $data['keterangan'] ?? null;
        $jenisBangunan = $data['jenisBangunan'];
        $jenisLayanan = $data['jenisLayanan'] ?? 1;
        $maps = $data['maps'] ?? null;
        $status = 1;

        try {
            $lokasi = new Lokasi();
            $lokasi->NamaLokasi = $NamaLokasi;
            $lokasi->alamat = $alamat;
            $lokasi->RT = $RT;
            $lokasi->RW = $RW;
            $lokasi->idProvince = $idProvince;
            $lokasi->idRegencies = $idRegencies;
            $lokasi->idCustomer = $idCustomer;
            $lokasi->idDistricts = $idDistricts;
            $lokasi->idVillages = $idVillages;
            $lokasi->namaPIC = $namaPIC;
            $lokasi->noHpPIC = $noHpPIC;
            $lokasi->emailPIC = $emailPIC;
            $lokasi->keterangan = $keterangan;
            $lokasi->jenisBangunan = $jenisBangunan;
            $lokasi->jenisLayanan = $jenisLayanan;
            $lokasi->maps = $maps;
            $lokasi->status = $status;
            $lokasi->save();

            return $this->created($lokasi, 'Lokasi created successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to create lokasi: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        $this->auth->authenticate();
        try{
            $lokasi = Lokasi::find($id);

            if(!$lokasi){
                return $this->notFound('Lokasi not found');
            }

            if($lokasi->delete()){
                return $this->success(null,'Lokasi deleted successfully');
            }else{
                return $this->serverError('Failed to delete lokasi');
            }
        }catch (Exception $e) {
            return $this->serverError('Failed to delete lokasi: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, [
            'NamaLokasi',
            'alamat',
            'RT',
            'RW',
            'idProvince',
            'idRegencies',
            'idCustomer',
            'idDistricts',
            'idVillages',
            'namaPIC',
            'noHpPIC',
            'jenisBangunan',
        ]);

        if ($validation) return $validation;

        $NamaLokasi = $data['NamaLokasi'];
        $alamat = $data['alamat'];
        $RT = $data['RT'];
        $RW = $data['RW'];
        $idProvince = $data['idProvince'];
        $idRegencies = $data['idRegencies'];
        $idCustomer = $data['idCustomer'];
        $idDistricts = $data['idDistricts'];
        $idVillages = $data['idVillages'];
        $namaPIC = $data['namaPIC'];
        $noHpPIC = $data['noHpPIC'];
        $emailPIC = $data['emailPIC'] ?? null;
        $keterangan = $data['keterangan'] ?? null;
        $jenisBangunan = $data['jenisBangunan'];
        $jenisLayanan = $data['jenisLayanan'] ?? 1;
        $maps = $data['maps'] ?? null;
        $status = 1;

        try {
            $lokasi = Lokasi::find($id);

            if(!$lokasi){
                return $this->notFound('Lokasi not found');
            }

            $lokasi->NamaLokasi = $NamaLokasi;
            $lokasi->alamat = $alamat;
            $lokasi->RT = $RT;
            $lokasi->RW = $RW;
            $lokasi->idProvince = $idProvince;
            $lokasi->idRegencies = $idRegencies;
            $lokasi->idCustomer = $idCustomer;
            $lokasi->idDistricts = $idDistricts;
            $lokasi->idVillages = $idVillages;
            $lokasi->namaPIC = $namaPIC;
            $lokasi->noHpPIC = $noHpPIC;
            $lokasi->emailPIC = $emailPIC;
            $lokasi->keterangan = $keterangan;
            $lokasi->jenisBangunan = $jenisBangunan;
            $lokasi->jenisLayanan = $jenisLayanan;
            $lokasi->maps = $maps;
            $lokasi->status = $status;
            $lokasi->save();

            return $this->success($lokasi, 'Lokasi updated successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to update lokasi: ' . $e->getMessage());
        }
    }

    public function detail($id) {
        $this->auth->authenticate();
        
        try {
            $lokasi = Lokasi::find($id);

            if(!$lokasi){
                return $this->notFound('Lokasi not found');
            }

            return $this->success($lokasi, 'Lokasi found successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch lokasi: ' . $e->getMessage());
        }
    }

    public function getProvinces() {
        $this->auth->authenticate();
        
        try {
            $provinces = Provinces::get();
            return $this->success($provinces, 'Provinces fetched successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user orders: ' . $e->getMessage());
        }
    }

    public function getRegencies($id) {
        $this->auth->authenticate();
        
        try {
            $regencies = Regencies::where('province_id', $id)->get();
            return $this->success($regencies, 'Regencies fetched successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user orders: ' . $e->getMessage());
        }
    }

    public function getDistricts($id) {
        $this->auth->authenticate();
        
        try {
            $districts = Districts::where('regency_id', $id)->get();
            return $this->success($districts, 'Districts fetched successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user orders: ' . $e->getMessage());
        }
    }

    public function getVillages($id) {
        $this->auth->authenticate();
        
        try {
            $villages = Villages::where('district_id', $id)->get();
            return $this->success($villages, 'Villages fetched successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user orders: ' . $e->getMessage());
        }
    }

}