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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->decimal('gross_salary_month', 10, 2);
            $table->decimal('food_allowance_month', 10, 2);
            $table->decimal('additional_subsidies', 10, 2);
            $table->decimal('social_security', 10, 2);
            $table->decimal('mandatory_ensurance', 10, 2);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes para melhor performance
            $table->index(['company_id', 'date']);
            $table->index(['employee_id', 'date']);
            $table->index(['created_by', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};