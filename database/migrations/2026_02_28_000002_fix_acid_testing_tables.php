<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix acid_test_header — add softDeletes + timestamps
        Schema::table('acid_test_header', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Fix acid_test_percentage_details — add timestamps + softDeletes
        // + move avg_pallet_and_foreign_weight here from header
        Schema::table('acid_test_percentage_details', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
        });

        // Fix acid_stock_conditions — add timestamps
        Schema::table('acid_stock_conditions', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('acid_test_header', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('acid_test_percentage_details', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
        Schema::table('acid_stock_conditions', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
