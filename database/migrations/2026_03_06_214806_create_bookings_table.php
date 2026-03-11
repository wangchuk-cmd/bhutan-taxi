<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('passenger_id')->constrained('users');
            $table->json('passengers_info');
            $table->enum('booking_type',['shared','full']);
            $table->integer('seats_booked');
            $table->enum('payment_status',['pending','paid','failed'])->default('pending');
            $table->timestamp('payment_time')->nullable();
            $table->timestamp('booking_time')->useCurrent();
            $table->timestamp('cancellation_time')->nullable();
            $table->enum('refund_status',['none','pending','refunded'])->default('none');
            $table->enum('status',['active','cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
