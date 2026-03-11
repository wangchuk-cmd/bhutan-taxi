<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->nullable()->constrained()->onDelete('set null');
            $table->string('origin_dzongkhag');
            $table->string('destination_dzongkhag');
            $table->dateTime('departure_datetime');
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->decimal('price_per_seat', 10, 2);
            $table->decimal('full_taxi_price', 10, 2);
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('trips'); }
};
