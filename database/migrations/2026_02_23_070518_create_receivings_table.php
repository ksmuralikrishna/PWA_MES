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
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('supplier');
            $table->string('material');
            $table->integer('invoice_qty');
            $table->integer('received_qty');
            $table->string('unit');
            $table->string('vehicle_number');
            $table->string('lot_no')->unique();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('operator_id');
            $table->enum('status', ['pending', 'synced'])->default('synced');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
