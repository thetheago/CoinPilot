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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('account_id')->constrained('accounts');
            $table->bigInteger('version')->default(0);
            $table->json('payload');
            $table->timestamps();

            $table->unique(['account_id', 'version']);
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
