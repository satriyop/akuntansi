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
        Schema::create('quotation_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Activity type: call, email, meeting, note, status_change, follow_up_scheduled
            $table->string('type', 30);

            // Contact method for call/email activities
            $table->string('contact_method', 20)->nullable(); // phone, whatsapp, email, visit

            // Activity details
            $table->string('subject', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('activity_at');

            // Duration for calls/meetings (in minutes)
            $table->unsignedSmallInteger('duration_minutes')->nullable();

            // Contact person
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_phone', 30)->nullable();

            // Follow-up scheduling
            $table->timestamp('next_follow_up_at')->nullable();
            $table->string('follow_up_type', 20)->nullable(); // call, email, meeting, visit

            // Outcome of activity
            $table->string('outcome', 30)->nullable(); // positive, neutral, negative, no_answer

            $table->timestamps();

            // Indexes
            $table->index(['quotation_id', 'activity_at']);
            $table->index('type');
            $table->index('activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_activities');
    }
};
