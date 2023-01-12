<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdRequestStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_request_status', function (Blueprint $table) {
            $table->id();
            $table->integer('app_request_id')->nullable();
            $table->integer('ad_status')->default(1)->comment();
            $table->float('duration_last_status')->nullable();
            $table->text('duration_with_request')->nullable();
            $table->dateTime('request_time')->default(\Carbon\Carbon::now());
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
        Schema::dropIfExists('ad_request_status');
    }
}
