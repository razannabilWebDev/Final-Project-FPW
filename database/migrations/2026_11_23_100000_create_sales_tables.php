<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->enum('type', ['menu', 'manual']);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'transfer', 'qris']);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('stock_deducted_at')->nullable();
            $table->timestamp('reversal_done_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'completed_at']);
        });

        Schema::create('sale_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained()->restrictOnDelete();
            // FK ke menus ditambahkan terpisah bila tabel menu (Anggota 2) sudah tersedia.
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->string('menu_name_snapshot');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price_snapshot', 12, 2);
            $table->decimal('hpp_snapshot', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('sale_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained()->restrictOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        if (Schema::hasTable('menus')) {
            Schema::table('sale_items', function (Blueprint $table): void {
                $table->foreign('menu_id')->references('id')->on('menus')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_status_histories');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
