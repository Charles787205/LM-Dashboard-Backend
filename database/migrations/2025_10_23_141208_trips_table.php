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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');

            $table->integer('two_w')->default(0);
            $table->integer('three_w')->default(0);
            $table->integer('four_w')->default(0);

            $table->timestamps();

            // foreign key
            $table->foreign('report_id')->references('id')->on('hub_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
