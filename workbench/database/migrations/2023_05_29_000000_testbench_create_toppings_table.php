<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Pizza;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('toppings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pizza::class)
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('name');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toppings');
    }
};
