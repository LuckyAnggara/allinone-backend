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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique()->index();
            $table->string('invoice');
            $table->integer('customer_id');
            $table->double('total')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax')->default(0);
            $table->enum('shipping_type', ['TAKE AWAY', 'DELIVERY']);
            $table->double('shipping_cost')->default(0);
            $table->double('etc_cost')->default(0);
            $table->string('etc_cost_desc')->nullable();
            $table->double('grand_total')->default(0);
            $table->boolean('credit')->default(false);
            $table->double('remaining_credit')->default(0);
            $table->enum('payment_type', ['CASH', 'TRANSFER', 'QR CODE', 'DIGITAL PAYMENT']);
            $table->enum('payment_status', ['LUNAS', 'BELUM LUNAS']);
            $table->boolean('global_tax')->default(false);
            $table->integer('global_tax_id')->default(1);
            $table->date('due_date')->nullable();
            $table->boolean('retur_status')->default(false);
            $table->date('retur_at')->nullable();
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
        Schema::dropIfExists('sales');
    }
};
