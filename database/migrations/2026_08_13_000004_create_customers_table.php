<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('industry')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->enum('segment', ['strategic', 'high_value', 'medium_value', 'low_value'])->default('medium_value');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('region');
            $table->index('industry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
