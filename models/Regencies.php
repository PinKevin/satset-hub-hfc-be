<?php

use Illuminate\Database\Eloquent\Model;

class Regencies extends Model
{
    protected $table = 'regencies';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'province_id', 'name'];

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }

    public function districts()
    {
        return $this->hasMany(Districts::class, 'regency_id');
    }
}
