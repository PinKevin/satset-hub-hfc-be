<?php

use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $table = 'districts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'regency_id', 'name'];

    public function regency()
    {
        return $this->belongsTo(Regencies::class, 'regency_id');
    }

    public function villages()
    {
        return $this->hasMany(Villages::class, 'district_id');
    }
}
