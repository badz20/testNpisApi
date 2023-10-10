<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('no_telefon')->nullable();
            $table->string('catatan')->nullable();
            $table->foreignId('jenis_pengguna_id')->nullable()->constrained('ref_jenis_pengguna');
            $table->foreignId('agensi_id')->nullable()->constrained('ref_agensi');
            $table->foreignId('jawatan_id')->nullable()->constrained('ref_jawatan');
            $table->foreignId('jabatan_id')->nullable()->constrained('ref_jabatan');
            $table->foreignId('negeri_id')->nullable()->constrained('ref_negeri');
            $table->foreignId('gred_jawatan_id')->nullable()->constrained('ref_gred');
            $table->foreignId('bahagian_id')->nullable()->constrained('ref_bahagian');
            $table->foreignId('daerah_id')->nullable()->constrained('ref_daerah');            
            $table->string('no_ic')->nullable();
            $table->boolean('status_pengguna_id')->default(0);
            $table->boolean('first_time')->default(1);
            $table->string('gambar_profil')->nullable();
            $table->string('document_path')->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            //$table->boolean('is_superadmin')->default(0);
            $table->boolean('row_status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
