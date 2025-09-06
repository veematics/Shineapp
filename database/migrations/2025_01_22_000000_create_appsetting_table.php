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
        Schema::create('appsetting', function (Blueprint $table) {
            $table->id('appID');
            $table->string('appName', 50)->default('0');
            $table->string('appHeadline', 50)->default('0');
            $table->string('appLogoBig', 255)->default('0');
            $table->string('appLogoSmall', 255)->default('0');
            $table->string('appLogoBigDark', 255)->default('0');
            $table->string('appLogoSmallDark', 255)->default('0');
            $table->string('appopenaikey', 100)->default('0');
            $table->string('appdeepseekkey', 100)->default('0');
            $table->float('appAITemperature')->default(0.7);
            $table->integer('appAiMaxToken')->default(2000);
            $table->text('appAIDefaultModel')->nullable(); // JSON format for models configuration
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appsetting');
    }
};