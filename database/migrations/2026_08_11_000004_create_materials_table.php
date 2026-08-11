<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('code_sku', 50)->unique();
            $table->string('name', 150);
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('unit_measure', 10)->default('UN');
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->string('ca_number', 50)->nullable();
            $table->date('ca_validity')->nullable();
            $table->boolean('is_returnable')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
