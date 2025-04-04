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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('retailer_id')->constrained('companies');
            $table->foreignId('supplier_id')->constrained('companies');
            $table->foreignId('warehouse_inventory_id')->constrained('inventories');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->integer('min_order');
            $table->integer('max_order');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['retailer_id', 'supplier_id', 'warehouse_inventory_id'], 'cart_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
