<?php

require_once __DIR__ . '/../config/eloquent.php';

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});

echo "Posts table created successfully\n";
?>
