<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();

            // Monthly budget amounts (for flexible budgeting)
            $table->bigInteger('jan_amount')->default(0);
            $table->bigInteger('feb_amount')->default(0);
            $table->bigInteger('mar_amount')->default(0);
            $table->bigInteger('apr_amount')->default(0);
            $table->bigInteger('may_amount')->default(0);
            $table->bigInteger('jun_amount')->default(0);
            $table->bigInteger('jul_amount')->default(0);
            $table->bigInteger('aug_amount')->default(0);
            $table->bigInteger('sep_amount')->default(0);
            $table->bigInteger('oct_amount')->default(0);
            $table->bigInteger('nov_amount')->default(0);
            $table->bigInteger('dec_amount')->default(0);

            // Total for quick access
            $table->bigInteger('annual_amount')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            // Each account can only appear once per budget
            $table->unique(['budget_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
    }
};
