<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->integer('app_id')->nullable();
            $table->enum('role', [1,2,3])->nullable()->comment('1->Admin,2->Sub Admin,3->End User');
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('email')->nullable();
            $table->text('password')->nullable();
            $table->text('decrypted_password')->nullable();
            $table->text('profile_pic')->nullable();
            $table->integer('device_id')->nullable();
            $table->enum('device_type', [1,2])->nullable()->comment('1->Android,2->iOS');
            $table->integer('device_company')->nullable();
            $table->text('device_model')->nullable();
            $table->text('device_os_version')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->dateTime('last_open_time')->default(\Carbon\Carbon::now());
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
        Schema::dropIfExists('users');
    }
}
