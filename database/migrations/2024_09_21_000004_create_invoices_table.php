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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('0=None, 1=Expense, 2=Sale');
            $table->foreignId('category_id')->nullable()->constrained('invoice_categories')->onDelete('set null');
            $table->string('atcud')->unique();
            $table->string('nif', 12);
            $table->date('date');
            $table->decimal('total_iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('files');
            $table->json('metadata');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes para melhor performance
            $table->index(['type', 'date']);
            $table->index(['company_id', 'date']);
            $table->index(['created_by', 'date']);
            $table->index('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};