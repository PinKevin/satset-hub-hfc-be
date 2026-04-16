<?php

use Illuminate\Database\Eloquent\Model;

class LogInquiry extends Model
{
    protected $table = 'tb_log_inquiry';
    public $timestamps = false;
    protected $fillable = ['idInquiry', 'tgl', 'ket', 'idUser', 'status'];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'idInquiry');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'idUser', 'Id');
    }
}
