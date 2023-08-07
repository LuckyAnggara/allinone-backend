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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('sku')->unique();
            $table->integer('unit_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('brand')->nullable();
            $table->double('balance')->default(0);
            $table->double('qty_minimum')->default(0);
            $table->boolean('iSell')->default(true);
            $table->boolean('iBuy')->default(true);
            $table->double('selling_price')->default(0);
            $table->double('buying_price')->default(0);
            $table->integer('selling_tax_id')->default(0);
            $table->integer('buying_tax_id')->default(0);
            $table->string('description')->default(0);
            $table->integer('warehouse_id')->default(0);
            $table->string('rack')->nullable();
            $table->boolean('archive')->default(false);
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
        Schema::dropIfExists('items');
    }
};
