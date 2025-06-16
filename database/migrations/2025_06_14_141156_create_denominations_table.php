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
        Schema::create('denominations', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 10, 2);
            $table->integer('count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreignId('currency_id')->constrained()->onDelete('cascade');

            $table->unique(['currency_id', 'value']);
            $table->index(['currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denominations');
    }
};
