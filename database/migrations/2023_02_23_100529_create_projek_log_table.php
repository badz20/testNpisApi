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
        Schema::connection(env('DB_CONNECTION_AUDIT'))->dropIfExists('projek_log');

        Schema::connection(env('DB_CONNECTION_AUDIT'))->create('projek_log', function (Blueprint $table) {
            $table->id();
            $table->integer('projek_id');
            $table->integer('user_id');
            $table->string('user_ic_no',20);
            $table->string('user_jawatan',400);
            $table->string('user_name',200);
            $table->string('modul')->nullable();
            $table->string('section_name')->nullable();
            $table->string('no_rujukan')->nullable();
            $table->timestamp('Created_on')->useCurrent();;
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
        // Schema::dropIfExists('projek_log');
    }
};
