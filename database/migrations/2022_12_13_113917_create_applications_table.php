<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->text('app_name')->nullable();
            $table->text('app_bundle')->nullable();
            $table->text('app_icon')->nullable();
            $table->text('interstitial1')->nullable();
            $table->text('interstitial2')->nullable();
            $table->text('native1')->nullable();
            $table->text('native2')->nullable();
            $table->text('banner1')->nullable();
            $table->text('banner2')->nullable();
            $table->text('app_open1')->nullable();
            $table->text('app_open2')->nullable();
            $table->integer('click_event')->nullable();
            $table->text('interval_time')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            $table->dateTime('updated_at')->default(null)->onUpdate(\Carbon\Carbon::now());
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
