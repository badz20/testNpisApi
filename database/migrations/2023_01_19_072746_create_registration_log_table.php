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
        Schema::connection(env('DB_CONNECTION_AUDIT'))->dropIfExists('registration_log');

        Schema::connection(env('DB_CONNECTION_AUDIT'))->create('registration_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_ic_no',20);
            $table->string('user_jawatan',200);
            $table->string('user_name',200);
            $table->integer('updated_by_user_id');
            $table->string('updated_by_user_ic_no',20);
            $table->string('updated_by_user_jawatan',200)->nullable();
            $table->string('updated_by_user_name',200);
            $table->string('action_taken',100)->nullable();
            $table->dateTime('created_on')->nullable();
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
        // Schema::connection(env('DB_CONNECTION_AUDIT'))->dropIfExists('registration_log');
    }
};
