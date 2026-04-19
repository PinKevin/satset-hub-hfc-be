<?php

use Illuminate\Database\Eloquent\Model;

class PromosiModal extends Model {
    protected $table = 'promosi_modals';
    protected $fillable = [
        'id',
        'judul',
        'konten',
        'gambar',
        'tipe',
        'primary_button_text',
        'primary_button_link',
        'primary_button_color',
        'secondary_button_text',
        'secondary_button_link',
        'secondary_button_color',
        'show_close_button',
        'auto_close_delay',
        'is_active',
        'mulai_tampil',
        'selesai_tampil',
    ];
    
    public $timestamps = true;

    /**
     * Check if the modal is currently showing (active and within date range)
     */
    public function isCurrentlyShowing(): bool
    {
        // Check is_active
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Check mulai_tampil (start date)
        if ($this->mulai_tampil && $now < $this->mulai_tampil) {
            return false;
        }

        // Check selesai_tampil (end date)
        if ($this->selesai_tampil && $now > $this->selesai_tampil) {
            return false;
        }

        return true;
    }
}