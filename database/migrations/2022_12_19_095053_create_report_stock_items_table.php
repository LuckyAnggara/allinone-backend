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
        Schema::create('report_stock_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->double('balance')->default(0);
            $table->double('price')->default(0);
            $table->double('total')->default(0);
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
        Schema::dropIfExists('report_stock_items');
    }
};
