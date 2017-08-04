<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelTrackingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_tracking_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('trackable_id')->unsigned();
            $table->string('trackable_type');
            $table->string('action');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('model_tracking_logs');
    }
}
