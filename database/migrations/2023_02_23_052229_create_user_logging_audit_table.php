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
        Schema::connection(env('DB_CONNECTION_AUDIT'))->dropIfExists('user_logging_audit');

        Schema::connection(env('DB_CONNECTION_AUDIT'))->create('user_logging_audit', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_ic_no',20);
            $table->string('user_jawatan',400);
            $table->string('user_name',200);
            $table->boolean('jenis_pengguna_id')->default(0);
            $table->boolean('auditable_type')->nullable();
            $table->string('event')->nullable();
            $table->string('url')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
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
        // Schema::dropIfExists('user_logging_audit');
    }
};
