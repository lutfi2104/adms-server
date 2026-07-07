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
        Schema::create('access_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->dateTime('entry_time')->nullable();
            $table->dateTime('exit_time')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('status')->default('open'); // 'open', 'completed', 'no_exit', 'no_entry'
            $table->string('entry_sn')->nullable();
            $table->string('exit_sn')->nullable();
            $table->timestamps();

            // Indexes for speed
            $table->index('employee_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_sessions');
    }
};
