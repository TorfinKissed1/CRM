<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('label')->nullable();        // что за операция (услуга/комментарий)
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('type', 10)->default('income'); // income|expense
            $table->string('method')->nullable();        // нал/карта/перевод
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
