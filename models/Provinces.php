<?php

use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
    protected $table = 'provinces';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name'];

    public function regencies()
    {
        return $this->hasMany(Regencies::class, 'province_id');
    }
}
