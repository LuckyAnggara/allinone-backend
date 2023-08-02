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
        Schema::create('shipping_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('shipping_vendor_id');
            $table->double('price');
            $table->string('receiver_name');
            $table->string('receiver_address');
            $table->string('receiver_postal_code');
            $table->string('receiver_kelurahan');
            $table->string('receiver_kecamatan');
            $table->string('receiver_kota');
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone_number')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_address')->nullable();
            $table->string('sender_phone_number')->nullable();
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
        Schema::dropIfExists('shipping_details');
    }
};
