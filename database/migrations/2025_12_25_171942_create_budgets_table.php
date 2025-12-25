<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('fiscal_period_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['annual', 'quarterly', 'monthly'])->default('annual');
            $table->enum('status', ['draft', 'approved', 'closed'])->default('draft');
            $table->bigInteger('total_revenue')->default(0);
            $table->bigInteger('total_expense')->default(0);
            $table->bigInteger('net_budget')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fiscal_period_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
