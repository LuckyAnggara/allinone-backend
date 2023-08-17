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
  Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');  // Type of notification, e.g., 'App\Notifications\OrderShipped'
            $table->text('message');  // Notification message
            $table->string('link')->nullable();  // Link associated with the notification
            $table->unsignedBigInteger('notifiable_id');  // Foreign key to the user or other notifiable entity
            $table->string('status')->default('unread');  // Status of the notification (unread, read, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
