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
        Schema::create('snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('account_id')->constrained('accounts');
            $table->json('state'); // Estado da lida inteira da minha stack de events, exemplo : { amount: 300 }
            $table->unsignedBigInteger('version');
            $table->timestamps();

            $table->index(['account_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapshots');
    }
};
