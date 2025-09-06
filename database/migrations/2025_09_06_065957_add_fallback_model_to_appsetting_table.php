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
        Schema::table('appsetting', function (Blueprint $table) {
            $table->json('appAIFallbackModel')->nullable()->after('appAIDefaultModel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appsetting', function (Blueprint $table) {
            $table->dropColumn('appAIFallbackModel');
        });
    }
};
