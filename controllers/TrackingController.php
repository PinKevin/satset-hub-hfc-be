<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrackingController extends BaseController
{
    /**
     * Show tracking timeline for customer in JSON format
     */
    public function index($token)
    {
        try {
            $inquiry = Inquiry::with([
                'customer',
                'layanan',
                'subLayanan',
                'statusRelation',
                'lokasi.lokasiDetails',
                'order',
                'office.bankAccounts',
            ])
            ->where('tracking_token', $token)
            ->first();

            if (!$inquiry) {
                return $this->notFound('Tracking data not found');
            }

            $logs = LogInquiry::with('karyawan')
                ->where('idInquiry', $inquiry->id)
                ->orderBy('tgl', 'desc')
                ->get();

            $rangers = [];
            if ($inquiry->order) {
                $rangers = Ranger::where('idOrder', $inquiry->order->id)
                    ->orderBy('tgl', 'asc')
                    ->get();

                foreach ($rangers as $r) {
                    $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
                    // Process fotoUrl (Masuk)
                    if ($r->fotoUrl) {
                        if (!filter_var($r->fotoUrl, FILTER_VALIDATE_URL)) {
                            $r->fotoUrl = rtrim($appUrl, '/') . '/storage/' . ltrim($r->fotoUrl, '/');
                        }
                    }

                    // Process fotoUrl2 (Pulang)
                    if ($r->fotoUrl2) {
                        if (!filter_var($r->fotoUrl2, FILTER_VALIDATE_URL)) {
                            $r->fotoUrl2 = rtrim($appUrl, '/') . '/storage/' . ltrim($r->fotoUrl2, '/');
                        }
                    }
                }
            }

            // Determine Current Step
            $currentStep = 1;
            if ($inquiry->status != 60) { 
                $currentStep = 2;
            }

            $originLat = null;
            $originLng = null;
            $destLat = null;
            $destLng = null;

            if ($inquiry->order) {
                $currentStep = 3;
                foreach ($logs as $log) {
                    if (stripos($log->ket, 'Clock In') !== false || stripos($log->ket, 'Mulai Pengerjaan') !== false) {
                        $currentStep = max($currentStep, 5);
                    }
                    if (stripos($log->ket, 'Menuju') !== false || stripos($log->ket, 'Otw') !== false || stripos($log->ket, 'perjalanan') !== false) {
                        $currentStep = max($currentStep, 4);
                    }
                    if (stripos($log->ket, 'Selesai') !== false) {
                        $currentStep = 6;
                    }
                }
                
                if ($inquiry->status == 64) { 
                    $currentStep = 6;
                }

                $latestRanger = Ranger::where('idOrder', $inquiry->order->id)
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($latestRanger) {
                    $originLat = $latestRanger->lat;
                    $originLng = $latestRanger->lng;
                }
            }

            if ($inquiry->lokasi && $inquiry->lokasi->lokasiDetails && $inquiry->lokasi->lokasiDetails->count() > 0) {
                $detail = $inquiry->lokasi->lokasiDetails->first();
                $destLat = $detail->Lat;
                $destLng = $detail->Lng;
            }

            $rangerStatusLabel = 'Ranger Sedang Menuju Lokasi';
            if ($currentStep >= 6) {
                 $rangerStatusLabel = 'Pekerjaan Selesai';
            } elseif ($currentStep == 5) {
                 $rangerStatusLabel = 'Ranger Sedang Mengerjakan';
            } elseif ($currentStep == 4) {
                 $rangerStatusLabel = 'Ranger Sedang Menuju Lokasi';
            }
            
            $order = $inquiry->order;
            $paymentMethod = $order->payment_method ?? $inquiry->payment_method ?? null;

            $reminderSettings = $order ? ($order->reminder_settings ?? []) : [];
            $cashEntrusted = false;
            if (is_array($reminderSettings) && array_key_exists('cash_entrusted_to_ranger', $reminderSettings)) {
                $cashEntrusted = (bool) $reminderSettings['cash_entrusted_to_ranger'];
            }

            $paymentConfirmed = false;
            if ($paymentMethod === 'Tunai') {
                $paymentConfirmed = $cashEntrusted;
            } elseif ($paymentMethod && strpos($paymentMethod, 'Non-Tunai') === 0) {
                $paymentConfirmed = $order && !empty($order->proof_of_payment);
            }

            return $this->success([
                'inquiry' => $inquiry,
                'logs' => $logs,
                'rangers' => $rangers,
                'currentStep' => $currentStep,
                'location' => [
                    'origin' => ['lat' => $originLat, 'lng' => $originLng],
                    'destination' => ['lat' => $destLat, 'lng' => $destLng],
                    'rangerStatusLabel' => $rangerStatusLabel
                ],
                'payment' => [
                    'method' => $paymentMethod,
                    'confirmed' => $paymentConfirmed,
                    'cashEntrusted' => $cashEntrusted
                ]
            ], 'Tracking data fetched successfully');

        } catch (Exception $e) {
            return $this->serverError('Failed to fetch tracking data: ' . $e->getMessage());
        }
    }

    public function updatePayment($token)
    {
        $data = $this->getRequestData();
        // Since getRequestData might not handle multipart/form-data with files, we use $_POST/$_FILES for payment updates
        if (empty($data)) {
            $data = $_POST;
        }

        try {
            $inquiry = Inquiry::where('tracking_token', $token)->first();
            if (!$inquiry) {
                return $this->notFound('Inquiry data not found');
            }

            $order = Order::where('idInquiry', $inquiry->id)->first();
            if (!$order) {
                return $this->error('Order belum dibuat. Silakan hubungi admin.');
            }

            $paymentMethod = $order->payment_method;
            $isNonTunai = $paymentMethod && strpos($paymentMethod, 'Non-Tunai') === 0;
            $isTunai = $paymentMethod === 'Tunai';

            $shouldSendPaidInvoice = false;

            if ($isNonTunai) {
                if (!isset($_FILES['proof_of_payment']) && empty($order->proof_of_payment)) {
                    return $this->validationError('Upload bukti bayar wajib untuk metode pembayaran non-tunai.');
                }
                if (isset($_FILES['proof_of_payment'])) {
                    $shouldSendPaidInvoice = true;
                }
            }

            if ($isTunai) {
                $reminderSettings = $order->reminder_settings ?? [];
                if (!is_array($reminderSettings)) {
                    $reminderSettings = [];
                }

                $existingCashEntrusted = !empty($reminderSettings['cash_entrusted_to_ranger']);
                $cashEntrustedInput = isset($data['cash_entrusted_to_ranger']);

                if (!$existingCashEntrusted && !$cashEntrustedInput) {
                    return $this->error('Silakan konfirmasi bahwa pembayaran telah dititipkan ke ranger.');
                }

                if ($cashEntrustedInput) {
                    $reminderSettings['cash_entrusted_to_ranger'] = true;
                    $reminderSettings['payment_confirmed_at'] = date('Y-m-d H:i:s');
                    $order->reminder_settings = $reminderSettings;
                    if (!$existingCashEntrusted) {
                        $shouldSendPaidInvoice = true;
                    }
                }
            }

            if (isset($_FILES['proof_of_payment'])) {
                $file = $_FILES['proof_of_payment'];
                $filename = 'payment_' . time() . '_' . $file['name'];
                $targetDir = __DIR__ . '/../public/storage/payments/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                move_uploaded_file($file['tmp_name'], $targetDir . $filename);
                $order->proof_of_payment = 'storage/payments/' . $filename;
            }

            $order->save();

            if ($shouldSendPaidInvoice) {
                $this->sendInvoiceToCustomer($order, true);
            }

            return $this->success(null, 'Informasi pembayaran berhasil disimpan.');
        } catch (Exception $e) {
            return $this->serverError('Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    public function invoice($token, $type)
    {
        $type = strtolower($type);
        if (!in_array($type, ['unpaid', 'paid'], true)) {
            return $this->error('Invalid invoice type');
        }

        try {
            $inquiry = Inquiry::with(['customer', 'layanan', 'order', 'office.bankAccounts'])->where('tracking_token', $token)->first();
            if (!$inquiry) return $this->notFound();

            $order = $inquiry->order;
            if (!$order) return $this->error('Order not found');

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad((string)$order->id, 5, '0', STR_PAD_LEFT);
            $statusLabel = $type === 'paid' ? 'LUNAS' : 'BELUM LUNAS';

            $itemName = $inquiry->layanan->keterangan ?? 'Jasa Cleaning Satset Hub';
            $unitPrice = 150000; // Example static price
            $discountAmount = (float) ($inquiry->voucher_discount_amount ?? 0);
            $totalTagihan = $unitPrice - $discountAmount;

            return $this->success([
                'invoiceNumber' => $invoiceNumber,
                'status' => $statusLabel,
                'details' => [
                    'itemName' => $itemName,
                    'unitPrice' => $unitPrice,
                    'discountAmount' => $discountAmount,
                    'totalTagihan' => $totalTagihan
                ],
                'bankAccounts' => $inquiry->office ? $inquiry->office->bankAccounts : []
            ]);
        } catch (Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function rating($token)
    {
        try {
            $inquiry = Inquiry::with(['customer', 'order', 'layanan'])->where('tracking_token', $token)->first();
            if (!$inquiry) return $this->notFound();
            
            $rangerName = '';
            if ($inquiry->order && $inquiry->order->idMitra) {
                $mitraIds = json_decode($inquiry->order->idMitra, true)['id'] ?? [];
                if (!empty($mitraIds)) {
                    $ranger = Karyawan::find($mitraIds[0]);
                    if ($ranger) $rangerName = $ranger->Nama;
                }
            }

            return $this->success([
                'inquiryCode' => $inquiry->kodeInquiry,
                'rangerName' => $rangerName,
                'layanan' => $inquiry->layanan->namaLayanan ?? '-'
            ]);
        } catch (Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function processEarlyLeave($token)
    {
        $data = $this->getRequestData();
        $validation = $this->validateRequired($data, ['action']);
        if ($validation) return $validation;

        try {
            $inquiry = Inquiry::with(['customer'])->where('tracking_token', $token)->first();
            if (!$inquiry) return $this->notFound();

            $order = Order::where('idInquiry', $inquiry->id)->first();
            if (!$order) return $this->error('Order tidak ditemukan.');

            $isApproved = $data['action'] === 'approve';
            $statusText = $isApproved ? 'Disetujui' : 'Ditolak';
            $keterangan = $data['reason'] ?? '-';

            if ($isApproved) {
                $inquiry->status = 64; 
                $inquiry->save();
                
                $order->status = 64; 
                $order->save();

                Ranger::where('idOrder', $order->id)->update([
                    'jamKeluar' => date('Y-m-d H:i:s'),
                    'status' => 3 
                ]);

                $this->notifyCustomerCompletion($order);
            }

            LogInquiry::create([
                'idInquiry' => $inquiry->id,
                'idUser' => 0, 
                'tgl' => date('Y-m-d H:i:s'),
                'ket' => "Customer {$statusText} Permintaan Pulang Lebih Awal. Alasan: {$keterangan}",
                'status' => $inquiry->status
            ]);

            $this->notifyCS($inquiry, $statusText, $keterangan);

            return $this->success(null, 'Respon Anda telah tersimpan.');
        } catch (Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    private function notifyCustomerCompletion($order)
    {
        if (!$order || !$order->customer) return false;

        $customer = $order->customer;
        $tujuan = $customer->noHp;
        if (empty($tujuan)) return false;

        $namaCustomer = $customer->namaCustomer ?? 'Bapak/Ibu';
        $kodeInquiry = $order->inquiry->kodeInquiry ?? '-';
        
        $pesan = "Halo *$namaCustomer*,\n\nPekerjaan cleaning untuk pesanan *$kodeInquiry* telah SELESAI dikerjakan.\n\nTerima kasih telah menggunakan layanan Satset Hub. ✨";

        return $this->sendWhatsApp($tujuan, $pesan);
    }

    private function notifyCS($inquiry, $statusText, $reason)
    {
        $csList = collect();
        if ($inquiry->assign) {
            $assignedCS = Karyawan::find($inquiry->assign);
            if ($assignedCS && !empty($assignedCS->noHp)) $csList->push($assignedCS);
        }

        if ($csList->isEmpty()) {
            $backupCS = Karyawan::where('idFungsi', 17)->whereNotNull('noHp')->limit(3)->get();
            foreach($backupCS as $bcs) $csList->push($bcs);
        }

        foreach ($csList as $cs) {
            $namaCs = $cs->Nama;
            $namaCustomer = $inquiry->customer->namaCustomer ?? 'Customer';
            $pesan = "Halo *$namaCs*,\n\nCustomer *$namaCustomer* telah *$statusText* permintaan pulang lebih awal untuk Inquiry *$inquiry->kodeInquiry*.\n\nAlasan: $reason";
            $this->sendWhatsApp($cs->noHp, $pesan);
        }
    }

    private function sendWhatsApp($tujuan, $message)
    {
        $apiKey = $_ENV['WA_API_KEY'] ?? "kUBTLGmdCZjS45ajqB1yQYaNfiSqFpl4";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://depowawa.com/api/v1/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(["api_key" => $apiKey, "destination" => $tujuan, "message" => $message]),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        curl_exec($curl);
        curl_close($curl);
    }

    private function sendInvoiceToCustomer($order, bool $isPaid)
    {
        $order->loadMissing('customer', 'inquiry');
        if (!$order->customer || !$order->inquiry || !$order->inquiry->tracking_token) return;

        $customer = $order->customer;
        $tujuan = $customer->noHp;
        if (empty($tujuan)) return;

        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
        $trackingUrl = rtrim($appUrl, '/') . '/tracking/' . $order->inquiry->tracking_token;
        $statusText = $isPaid ? 'LUNAS' : 'belum lunas';

        $pesan = "Halo *" . ($customer->namaCustomer ?? 'Bapak/Ibu') . "*,\n\nBerikut invoice $statusText untuk pesanan *" . ($order->inquiry->kodeInquiry ?? '-') . "* Anda:\n\nCek detail di: " . $trackingUrl;

        $this->sendWhatsApp($tujuan, $pesan);
    }
}