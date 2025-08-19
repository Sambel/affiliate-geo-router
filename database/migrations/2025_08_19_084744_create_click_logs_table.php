<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_logs', function (Blueprint $table) {
            $table->id();
            $table->string('operator_slug');
            $table->string('country_code', 2)->nullable();
            $table->string('ip_hash');
            $table->string('user_agent_hash')->nullable();
            $table->string('referer')->nullable();
            $table->timestamp('clicked_at');
            
            $table->index(['operator_slug', 'clicked_at']);
            $table->index('country_code');
            $table->index('clicked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_logs');
    }
};