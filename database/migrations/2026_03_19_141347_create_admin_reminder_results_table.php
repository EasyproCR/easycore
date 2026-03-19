<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_reminder_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_reminder_id')->constrained()->cascadeOnDelete();
            $table->date('revision_date');
            $table->string('result');
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_reminder_results');
    }
};
