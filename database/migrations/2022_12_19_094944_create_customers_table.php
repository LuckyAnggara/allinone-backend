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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->index();
            $table->string('name');
            $table->enum('type', ['personal', 'company'])->default('personal');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('member')->default(0);
            $table->boolean('company')->default(0);
            $table->string('pic')->nullable();
            $table->string('postalcode')->nullable();
            $table->string('urban')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('city')->nullable();
            $table->integer('created_by');
            $table->integer('branch_id');
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
        Schema::dropIfExists('customers');
    }
};
