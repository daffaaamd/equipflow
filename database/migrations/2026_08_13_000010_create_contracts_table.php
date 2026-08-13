<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 30)->unique();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rental_rate', 16, 2)->default(0);
            $table->decimal('deposit', 16, 2)->default(0);
            $table->string('payment_terms', 50)->default('30 days');
            $table->decimal('contract_value', 16, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->date('signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};