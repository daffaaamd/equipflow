<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 30)->unique();
            $table->string('name');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('industry');
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('contract_value', 16, 2)->default(0);
            $table->enum('status', ['planning', 'active', 'completed', 'on_hold', 'cancelled'])->default('planning');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'industry']);
            $table->index('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
