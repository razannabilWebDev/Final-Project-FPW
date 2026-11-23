<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_forecasts', function (Blueprint $table): void {
            $table->id();
            $table->date('forecast_date');
            $table->string('method')->default('moving_average');
            $table->unsignedInteger('data_window')->default(28);
            $table->unsignedInteger('min_days')->default(7);
            $table->enum('data_status', ['cukup', 'terbatas'])->default('terbatas');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('adjusted_at')->nullable();
            $table->timestamps();
            $table->index(['forecast_date', 'method']);
        });

        Schema::create('production_forecast_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('forecast_id')->constrained('production_forecasts')->restrictOnDelete();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->decimal('predicted_quantity', 12, 3);
            $table->decimal('adjusted_quantity', 12, 3)->nullable();
            $table->text('adjustment_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_forecast_items');
        Schema::dropIfExists('production_forecasts');
    }
};
