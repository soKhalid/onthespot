<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('sku')->nullable();

            // Images
            $table->json('images')->nullable();

            // Fashion specific
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->integer('stock_quantity')->default(0);

            // Restaurant specific (menu items)
            $table->string('category')->nullable(); // appetizer, main, dessert, etc.
            $table->boolean('is_available')->default(true);

            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['brand_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
