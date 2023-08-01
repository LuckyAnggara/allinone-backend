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
            $table->string('receiver_email');
            $table->double('receiver_phone_number');
            $table->string('sender_name');
            $table->string('sender_address');
            $table->double('sender_phone_number');
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
