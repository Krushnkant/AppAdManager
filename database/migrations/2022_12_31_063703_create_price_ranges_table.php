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
        Schema::create('price_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('application_id')->nullable();
            $table->integer('package_type')->default(1)->comment('1->product,2->subscription');
            $table->string('title','255')->nullable();
            $table->string('price','255')->nullable();
            $table->string('value','255')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->timestamps();
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
        Schema::dropIfExists('price_ranges');
    }
};
