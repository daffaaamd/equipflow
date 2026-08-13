<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_number', 30)->unique();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->enum('type', ['preventive', 'corrective'])->default('preventive');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('technician')->nullable();
            $table->date('date');
            $table->decimal('cost', 16, 2)->default(0);
            $table->decimal('downtime_hours', 10, 2)->default(0);
            $table->text('parts_used')->nullable();
            $table->date('next_due_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['status', 'date']);
            $table->index('next_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};