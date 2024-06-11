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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('grand_total', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->string('payment_status')->nullable();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->enum('status',['new', 'processing', 'shipped', 'delivered', 'canceled'])->default('new');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('shipping_amount',10,2)->nullable();
            $table->foreignId('shipping_method_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
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
