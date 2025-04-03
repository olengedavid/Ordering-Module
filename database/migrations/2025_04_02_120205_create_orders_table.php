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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->uuid('uuid')->unique();
            $table->foreignId('retailer_id')->constrained('companies');
            $table->foreignId('supplier_id')->constrained('companies');
            $table->string('status')->default('PENDING');
            $table->string('payment_terms')->default('PAID_ON_DELIVERY');
            $table->text('delivery_address');
            $table->string('region');
            $table->date('expected_delivery_date');
            $table->date('delivered_date')->nullable();
            $table->string('delivery_confirmation_code')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
