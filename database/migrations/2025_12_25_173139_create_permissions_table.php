<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // e.g., 'invoices.create', 'reports.view'
            $table->string('display_name', 150);
            $table->string('group', 50); // e.g., 'invoices', 'reports', 'settings'
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
