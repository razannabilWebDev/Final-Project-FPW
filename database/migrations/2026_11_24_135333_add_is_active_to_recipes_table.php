<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('version');
            $table->index(['menu_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table): void {
            $table->dropIndex(['menu_id', 'is_active']);
            $table->dropColumn('is_active');
        });
    }
};
