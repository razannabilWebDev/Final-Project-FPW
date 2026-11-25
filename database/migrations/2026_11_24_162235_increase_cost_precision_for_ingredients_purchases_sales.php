<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Biaya per satuan dasar (gram/ml) perlu presisi lebih tinggi agar tidak
     * dibulatkan menjadi 0,00 saat disimpan maupun ditampilkan.
     */
    public function up(): void
    {
        Schema::table('ingredients', static function (Blueprint $table): void {
            $table->decimal('average_cost', 12, 4)->default(0)->change();
        });

        Schema::table('purchases', static function (Blueprint $table): void {
            $table->decimal('total', 14, 4)->change();
        });

        Schema::table('purchase_items', static function (Blueprint $table): void {
            $table->decimal('unit_cost', 12, 4)->change();
            $table->decimal('subtotal', 14, 4)->change();
        });

        Schema::table('sale_items', static function (Blueprint $table): void {
            $table->decimal('hpp_snapshot', 12, 4)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', static function (Blueprint $table): void {
            $table->decimal('hpp_snapshot', 12, 2)->change();
        });

        Schema::table('purchase_items', static function (Blueprint $table): void {
            $table->decimal('subtotal', 12, 2)->change();
            $table->decimal('unit_cost', 12, 2)->change();
        });

        Schema::table('purchases', static function (Blueprint $table): void {
            $table->decimal('total', 12, 2)->change();
        });

        Schema::table('ingredients', static function (Blueprint $table): void {
            $table->decimal('average_cost', 12, 2)->default(0)->change();
        });
    }
};
