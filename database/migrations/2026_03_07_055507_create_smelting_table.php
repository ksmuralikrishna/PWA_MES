<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. smelting_batches (header) ─────────────────────────────
        Schema::create('smelting_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->integer('rotary_no');
            $table->date('date');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->decimal('lpg_consumption', 10, 3)->nullable();
            $table->decimal('o2_consumption', 10, 3)->nullable();

            $table->decimal('id_fan_initial', 10, 3)->nullable();
            $table->decimal('id_fan_final', 10, 3)->nullable();
            $table->decimal('id_fan_consumption', 10, 3)->nullable();

            $table->decimal('rotary_power_initial', 10, 3)->nullable();
            $table->decimal('rotary_power_final', 10, 3)->nullable();
            $table->decimal('rotary_power_consumption', 10, 3)->nullable();

            $table->string('output_material')->nullable();
            $table->decimal('output_qty', 10, 3)->nullable();

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 2. smelting_raw_materials ────────────────────────────────
        Schema::create('smelting_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smelting_batch_id');  // no FK constraint
            $table->integer('raw_material_id');
            $table->unsignedBigInteger('bbsu_batch_id')->nullable();
            $table->string('bbsu_batch_no')->nullable();
            $table->decimal('raw_material_qty', 10, 3)->default(0);
            $table->decimal('raw_material_yield_pct', 8, 3)->default(0);
            $table->decimal('expected_output_qty', 10, 3)->default(0);

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 3. smelting_flux_chemicals ───────────────────────────────
        Schema::create('smelting_flux_chemicals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smelting_batch_id');  // no FK constraint
            $table->integer('chemical_id');
            $table->decimal('qty', 10, 3)->default(0);

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 4. smelting_process_details ──────────────────────────────
        Schema::create('smelting_process_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smelting_batch_id');  // no FK constraint
            $table->string('process_name');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->decimal('total_time', 10, 2)->default(0);
            $table->string('firing_mode')->nullable();

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 5. smelting_temperature_records ──────────────────────────
        Schema::create('smelting_temperature_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smelting_batch_id');  // no FK constraint
            $table->timestamp('record_time')->nullable();
            $table->decimal('inside_temp_before_charging', 10, 2)->nullable();
            $table->decimal('process_gas_chamber_temp', 10, 2)->nullable();
            $table->string('shell_temp')->nullable();
            $table->string('bag_house_temp')->nullable();

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── 6. smelting_output_blocks ─────────────────────────────────
        Schema::create('smelting_output_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('smelting_batch_id');  // no FK constraint
            $table->integer('material_id');
            $table->integer('block_sl_no');
            $table->decimal('block_weight', 10, 3)->default(0);

            $table->integer('status')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->integer('updated_by')->default(null)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smelting_output_blocks');
        Schema::dropIfExists('smelting_temperature_records');
        Schema::dropIfExists('smelting_process_details');
        Schema::dropIfExists('smelting_flux_chemicals');
        Schema::dropIfExists('smelting_raw_materials');
        Schema::dropIfExists('smelting_batches');
    }
};