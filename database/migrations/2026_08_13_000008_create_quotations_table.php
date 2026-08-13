<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 30)->unique();
            $table->foreignId('rental_request_id')->constrained('rental_requests');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('valid_until');
            $table->date('rental_period_start')->nullable();
            $table->date('rental_period_end')->nullable();
            $table->decimal('rental_rate', 16, 2)->default(0);
            $table->decimal('operator_cost', 16, 2)->default(0);
            $table->decimal('transportation_cost', 16, 2)->default(0);
            $table->decimal('fuel_cost', 16, 2)->default(0);
            $table->decimal('additional_service_cost', 16, 2)->default(0);
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(11);
            $table->decimal('subtotal', 16, 2)->default(0);
            $table->decimal('tax_amount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'accepted', 'revision', 'rejected', 'expired'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};