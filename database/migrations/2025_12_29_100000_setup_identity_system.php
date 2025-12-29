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
        // 1. Create Users Table with String Role
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // int auto PK
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'dosen', 'mahasiswa']);
            $table->timestamps();
        });

        // 2. Create Profile Tables with reference to users.id
        Schema::create('admin', function (Blueprint $table) {
            $table->string('kode_admin')->primary();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('tanggal_lahir');
            $table->string('no_telepon');
            $table->string('jenis_kelamin');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('dosen', function (Blueprint $table) {
            $table->string('nip')->primary();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin')->nullable();
            $table->string('email')->unique();
            $table->string('no_telepon')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('password');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('nrp')->primary();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama');
            $table->string('jurusan')->nullable();
            $table->string('email')->unique();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir');
            $table->text('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('password');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('dosen');
        Schema::dropIfExists('admin');
        Schema::dropIfExists('users');
    }
};
