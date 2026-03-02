<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── BBSU Header ───────────────────────────────────────────
        Schema::create('bbsu_headers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();             // BBSU-20260228-001
            $table->date('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();       // can be next day
            $table->enum('category', ['BBSU', 'Manual Cutting']);
            $table->decimal('total_input', 10, 3)->default(0);
            $table->decimal('avg_acid_pct', 5, 2)->default(0);
            $table->decimal('initial_power', 10, 2)->nullable();
            $table->decimal('final_power', 10, 2)->nullable();
            $table->decimal('total_power_consumption', 10, 2)->nullable();
            $table->decimal('total_output', 10, 3)->default(0);
            $table->decimal('yield', 5, 2)->default(0);
            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        // ── BBSU Input Lots (QTY Selection Window) ────────────────
        Schema::create('bbsu_input_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bbsu_header_id')->constrained('bbsu_headers')->cascadeOnDelete();
            $table->string('lot_number');
            $table->integer('pallet_no');
            $table->unsignedBigInteger('acid_test_detail_id');
            $table->string('ulab_type');
            $table->string('ulab_description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('available_qty', 10, 3);
            $table->decimal('assigned_qty', 10, 3);
            $table->decimal('acid_pct', 5, 2)->default(0);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });

        // ── BBSU Output Materials ─────────────────────────────────
        Schema::create('bbsu_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bbsu_header_id')->constrained('bbsu_headers')->cascadeOnDelete();
            $table->string('material_name');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('yield', 5, 2)->default(0);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });

        // ── Consumption Tracker ───────────────────────────────────
        Schema::create('bbsu_lot_consumption', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acid_test_detail_id')->unique();
            $table->decimal('total_assigned', 10, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbsu_lot_consumption');
        Schema::dropIfExists('bbsu_outputs');
        Schema::dropIfExists('bbsu_input_lots');
        Schema::dropIfExists('bbsu_headers');
    }
};