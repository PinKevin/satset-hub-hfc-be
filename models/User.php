<?php

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    
    public $timestamps = true;
    
    public function posts() {
        return $this->hasMany(Post::class);
    }
    
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
}
?>
