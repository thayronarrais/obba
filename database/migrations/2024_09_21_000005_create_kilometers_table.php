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
        Schema::create('kilometers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('licenseplate', 12);
            $table->date('date');
            $table->string('origin', 256);
            $table->string('destination', 256);
            $table->integer('kilometers');
            $table->string('reason', 256);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes para melhor performance
            $table->index(['company_id', 'date']);
            $table->index(['created_by', 'date']);
            $table->index('date');
            $table->index('licenseplate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kilometers');
    }
};