<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('contact_person');
            $table->string('contact_phone', 30)->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_location')->nullable();
            $table->boolean('operator_required')->default(false);
            $table->boolean('transportation_included')->default(false);
            $table->boolean('fuel_included')->default(false);
            $table->text('additional_requirements')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'quoted', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_requests');
    }
};
