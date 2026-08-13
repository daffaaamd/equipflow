<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('operator_code', 30)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('certification')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->string('license_number', 50)->nullable();
            $table->unsignedInteger('years_experience')->default(0);
            $table->foreignId('assigned_equipment_id')->nullable()->constrained('equipment')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->decimal('working_hours', 10, 2)->default(0);
            $table->enum('availability', ['available', 'assigned', 'on_leave'])->default('available');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('certification_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};