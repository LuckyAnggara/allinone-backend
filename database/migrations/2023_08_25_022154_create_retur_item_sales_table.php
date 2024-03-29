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
        Schema::create('retur_item_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('sale_detail_id');
            $table->integer('item_id');
            $table->double('qty');
            $table->double('price');
            $table->double('tax');
            $table->double('grand_total');
            $table->string('type');
            $table->string('notes');
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
        Schema::dropIfExists('retur_item_sales');
    }
};
