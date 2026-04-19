<?php

class PromoVoucherController extends BaseController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthMiddleware();
    }

    public function index() {
        $this->auth->authenticate();
        try{
            $campaigns = PmCampaigns::get();
            return $this->success($campaigns);
        }catch(Exception $e){
            return $this->serverError('Failed to fetch promo vouchers campaigns: ' . $e->getMessage());
        }
    }

    public function campaigns() {
        $this->auth->authenticate();
        try{
            $campaigns = PmCampaigns::get();
            return $this->success($campaigns);
        }catch(Exception $e){
            return $this->serverError('Failed to fetch promo vouchers campaigns: ' . $e->getMessage());

        }
    }

    public function campaignDetail($id) {
        $this->auth->authenticate();
        try{
            $campaign = PmCampaigns::find($id);
            if (!$campaign) {
                return $this->notFound('Campaign not found');
            }
            return $this->success($campaign);
        }catch(Exception $e){
            return $this->serverError('Failed to fetch promo voucher campaign detail: ' . $e->getMessage());
        }
    }

    public function claim() {
        $this->auth->authenticate();
        $data = $this->getRequestData();

        $validation = $this->validateRequired($data, ['campaign_id']);
        if ($validation) return $validation;

        try {
            $campaign = PmCampaigns::find($data['campaign_id']);
            if (!$campaign) {
                return $this->notFound('Campaign not found');
            }

            $existingVoucher = PmVouchers::where('campaign_id', $data['campaign_id'])
                ->where('current_owner_id', $this->auth->user()->id)
                ->first();

            if ($existingVoucher) {
                return $this->badRequest('You have already claimed this voucher');
            }

            $voucher = PmVouchers::create([
                'campaign_id' => $data['campaign_id'],
                'voucher_code' => strtoupper(uniqid('PROMO-')),
                'current_owner_id' => $this->auth->user()->id,
                'original_owner_id' => $this->auth->user()->id,
                'status' => 'claimed',
                'claimed_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->success($voucher, 'Voucher claimed successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to claim voucher: ' . $e->getMessage());
        }
    }

    public function transfer() {
        $this->auth->authenticate();
        $data = $this->getRequestData();

        $validation = $this->validateRequired($data, ['voucher_id', 'to_user_id']);
        if ($validation) return $validation;

        try {
            $voucher = PmVouchers::find($data['voucher_id']);
            if (!$voucher) {
                return $this->notFound('Voucher not found');
            }

            if ($voucher->current_owner_id != $this->auth->user()->id) {
                return $this->forbidden('You do not own this voucher');
            }

            $voucher->current_owner_id = $data['to_user_id'];
            $voucher->save();

            PmTransfers::create([
                'voucher_id' => $voucher->id,
                'from_user_id' => $this->auth->user()->id,
                'to_user_id' => $data['to_user_id'],
                'transfer_type' => 'manual',
                'notes' => 'Voucher transferred by user',
            ]);

            return $this->success($voucher, 'Voucher transferred successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to transfer voucher: ' . $e->getMessage());
        }
    }

    public function use() {
        $this->auth->authenticate();
        $data = $this->getRequestData();

        $validation = $this->validateRequired($data, ['voucher_id']);
        if ($validation) return $validation;

        try {
            $voucher = PmVouchers::find($data['voucher_id']);
            if (!$voucher) {
                return $this->notFound('Voucher not found');
            }

            if ($voucher->current_owner_id != $this->auth->user()->id) {
                return $this->forbidden('You do not own this voucher');
            }

            if ($voucher->status == 'used') {
                return $this->badRequest('Voucher has already been used');
            }

            $voucher->status = 'used';
            $voucher->used_at = date('Y-m-d H:i:s');
            $voucher->save();

            PmRedemptions::create([
                'voucher_id' => $voucher->id,
                'user_id' => $this->auth->user()->id,
                'redeemed_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->success($voucher, 'Voucher used successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to use voucher: ' . $e->getMessage());
        }
    }

    public function userVouchers() {
        $this->auth->authenticate();
        try {
            $vouchers = PmVouchers::where('current_owner_id', $this->auth->user()->id)->get();
            return $this->success($vouchers);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user vouchers: ' . $e->getMessage());
        }
    }

    public function userVoucherHistory() {
        $this->auth->authenticate();
        try {
            $vouchers = PmVouchers::where('original_owner_id', $this->auth->user()->id)->get();
            return $this->success($vouchers);
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch user voucher history: ' . $e->getMessage());
        }
    }

    public function generateImageVoucher($id) {
        $this->auth->authenticate();
        try {
            $voucher = PmVouchers::find($id);
            if (!$voucher) {
                return $this->notFound('Voucher not found');
            }

            $imagePath = 'path/to/generated/voucher/image/' . $voucher->voucher_code . '.png';
            
            return $this->success(['image_url' => $imagePath], 'Voucher image generated successfully');
        } catch (Exception $e) {
            return $this->serverError('Failed to generate voucher image: ' . $e->getMessage());
        }
    }
}