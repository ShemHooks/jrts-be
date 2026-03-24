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
        Schema::create('job_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'accepted', 'responding', 'in-bounded', 'done',])->default('pending');
            $table->foreignUuid('requested_from')->constrained('departments')->cascadeOnDelete();
            $table->string('look_for')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
