<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number', 30)->unique();
            $table->foreignId('contract_id')->constrained('contracts');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('pickup_location')->nullable();
            $table->string('destination')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->string('transport_vehicle')->nullable();
            $table->string('plate_number', 20)->nullable();
            $table->date('delivery_date');
            $table->date('estimated_arrival')->nullable();
            $table->enum('status', ['scheduled', 'preparing', 'in_transit', 'delivered', 'confirmed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'delivery_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};