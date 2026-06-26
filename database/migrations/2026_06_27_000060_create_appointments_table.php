<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 20)->default('planned'); // planned|completed|no_show|cancelled
            $table->decimal('price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['starts_at', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
