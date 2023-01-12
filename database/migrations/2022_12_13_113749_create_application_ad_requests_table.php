<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationAdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_ad_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('ad_type')->default(1)->comment('1->Interstitial, 2->AppOpen, 3->Native, 4->Banner, 5->Reward');
            $table->integer('ad_current_status')->default(1)->comment('1->Request, 2->Load, 3->Fail, 4->Show, 5->Click');
            $table->text('uniq_str_key')->nullable();
            $table->dateTime('request_time')->default(\Carbon\Carbon::now());
            $table->dateTime('created_at')->timestamps();
            $table->dateTime('updated_at')->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_ad_requests');
    }
}
