<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_code', 30)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('equipment_categories');
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('serial_number')->nullable();
            $table->decimal('operating_weight', 12, 2)->nullable();
            $table->decimal('engine_power', 12, 2)->nullable();
            $table->decimal('bucket_capacity', 10, 2)->nullable();
            $table->decimal('fuel_capacity', 10, 2)->nullable();
            $table->decimal('operating_hours', 12, 2)->default(0);
            $table->string('current_location')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair'])->default('good');
            $table->enum('status', ['available', 'rented', 'maintenance', 'unavailable'])->default('available');
            $table->decimal('daily_rate', 14, 2);
            $table->decimal('weekly_rate', 14, 2)->nullable();
            $table->decimal('monthly_rate', 14, 2)->nullable();
            $table->decimal('deposit', 14, 2)->nullable();
            $table->decimal('purchase_price', 16, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('next_service_hours', 12, 2)->nullable();
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'category_id']);
            $table->index(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
