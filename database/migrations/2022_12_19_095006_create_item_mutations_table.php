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
        Schema::create('item_mutations', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->double('debit_price')->default(0);
            $table->double('credit_price')->default(0);
            $table->double('balance')->default(0);
            $table->text('notes')->nullable();
            $table->text('link')->nullable();
            $table->integer('branch_id');
            $table->integer('created_by');
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
        Schema::dropIfExists('item_mutations');
    }
};
