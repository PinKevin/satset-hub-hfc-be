<?php

use Illuminate\Database\Eloquent\Model;

class Villages extends Model
{
    protected $table = 'villages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'district_id', 'name'];

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id');
    }
}
