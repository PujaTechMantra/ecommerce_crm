<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('designation')->nullable();
            $table->string('image')->nullable();
            $table->string('country_code', 20)->default('+91');
            $table->string('mobile')->nullable();
            $table->string('email');
            $table->string('password');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
         // Insert default Super Admin
        DB::table('admins')->insert([
            'name'       => 'Super Admin',
            'designation'=> null,
            'image'      => null,
            'country_code' => '+91',
            'mobile'     => null,
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('secret'), // change password later
            'status'     => 1,
            'last_login_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
