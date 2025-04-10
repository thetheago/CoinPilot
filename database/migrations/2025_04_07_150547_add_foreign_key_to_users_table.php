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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('name');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->unique('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['account_id']);
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
