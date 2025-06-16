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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 36);
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('method', 10);
            $table->string('endpoint', 255);
            $table->json('request_payload')->nullable();
            $table->integer('response_status');
            $table->json('response_payload')->nullable();
            $table->integer('latency_ms');
            $table->timestamps();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->index(['request_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
