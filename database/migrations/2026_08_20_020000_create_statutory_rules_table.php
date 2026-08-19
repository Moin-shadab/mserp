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
        if (!Schema::hasTable('statutory_rules')) {
            Schema::create('statutory_rules', function (Blueprint $table) {
                $table->id();
                $table->string('country', 3)->default('IND');
                $table->string('rule_key')->unique(); // e.g. PF_EMPLOYEE_RATE, ESI_EMPLOYEE_RATE, HRA_PERCENTAGE
                $table->string('rule_name');
                $table->decimal('percentage', 5, 2)->nullable(); // e.g. 12.00
                $table->decimal('fixed_amount', 15, 2)->nullable();
                $table->decimal('salary_threshold', 15, 2)->nullable();
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statutory_rules');
    }
};
