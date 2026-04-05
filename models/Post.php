<?php

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'user_id'];
    
    public $timestamps = true;
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}