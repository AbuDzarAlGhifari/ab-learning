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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method')->default('manual');
            $table->enum('status', ['pending', 'paid', 'rejected'])
                ->default('pending');
            $table->string('proof_url')->nullable();
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
