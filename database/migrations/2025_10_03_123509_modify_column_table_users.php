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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nama_depan');
            $table->string('nama_belakang')->after('nama_depan');
            $table->date('tanggal_lahir')->after('email')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->after('tanggal_lahir')->nullable();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
