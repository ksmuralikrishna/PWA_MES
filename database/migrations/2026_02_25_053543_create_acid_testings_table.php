<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('acid_testings', function (Blueprint $table) {
            $table->id();
            $table->date('test_date');
            $table->string('lot_number');
            $table->string('supplier');
            $table->string('vehicle_number')->nullable();
            $table->decimal('avg_pallet_weight', 10, 2);
            $table->decimal('foreign_material_weight', 10, 2)->nullable();
            $table->decimal('weigh_bridge_weight', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('acid_testings');
    }
};
