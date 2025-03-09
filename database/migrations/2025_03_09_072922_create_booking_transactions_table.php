<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trx_email');
            $table->string('phone_number');
            $table->boolean('is_paid');
            $table->string('proof');
            $table->unsignedBigInteger('total_amount');
            $table->foreignId('car_store_id')->constrained()->cascadeOnDelete(); // FK
            $table->foreignId('car_service_id')->constrained()->cascadeOnDelete(); // FK
            $table->date('started_at');
            $table->time('time_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};
