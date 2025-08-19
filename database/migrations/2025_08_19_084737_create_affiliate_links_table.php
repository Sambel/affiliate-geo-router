<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->text('url');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            $table->unique(['operator_id', 'country_id']);
            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};