<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->decimal('predicted_inflow', 15, 2);
            $table->decimal('predicted_outflow', 15, 2);
            $table->decimal('net_flow', 15, 2);
            $table->decimal('confidence_score', 5, 2);
            $table->json('analysis_metadata')->nullable();
            $table->timestamps();
            
            $table->index('forecast_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};