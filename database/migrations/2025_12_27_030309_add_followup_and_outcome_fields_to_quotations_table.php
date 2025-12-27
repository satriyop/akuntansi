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
        Schema::table('quotations', function (Blueprint $table) {
            // Follow-up tracking fields
            $table->timestamp('next_follow_up_at')->nullable()->after('status');
            $table->timestamp('last_contacted_at')->nullable()->after('next_follow_up_at');
            $table->foreignId('assigned_to')->nullable()->after('last_contacted_at')
                ->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('follow_up_count')->default(0)->after('assigned_to');

            // Win/Loss outcome fields
            $table->string('outcome', 20)->nullable()->after('rejection_reason'); // won, lost, cancelled
            $table->string('won_reason', 50)->nullable()->after('outcome');
            $table->string('lost_reason', 50)->nullable()->after('won_reason');
            $table->string('lost_to_competitor', 100)->nullable()->after('lost_reason');
            $table->text('outcome_notes')->nullable()->after('lost_to_competitor');
            $table->timestamp('outcome_at')->nullable()->after('outcome_notes');

            // Priority for follow-up
            $table->string('priority', 10)->default('normal')->after('follow_up_count'); // low, normal, high, urgent

            // Indexes
            $table->index('next_follow_up_at');
            $table->index('outcome');
            $table->index('priority');
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex(['next_follow_up_at']);
            $table->dropIndex(['outcome']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['assigned_to', 'status']);

            $table->dropForeign(['assigned_to']);

            $table->dropColumn([
                'next_follow_up_at',
                'last_contacted_at',
                'assigned_to',
                'follow_up_count',
                'priority',
                'outcome',
                'won_reason',
                'lost_reason',
                'lost_to_competitor',
                'outcome_notes',
                'outcome_at',
            ]);
        });
    }
};
