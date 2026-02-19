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
        Schema::create('hub_reports_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hub_id');

            // counts
            $table->integer('inbound')->default(0);
            $table->integer('outbound')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('backlogs')->default(0);
            $table->integer('failed')->default(0);
            $table->integer('misroutes')->default(0);

            // metrics / identifiers
            $table->date('date');
            $table->decimal('sdod')->nullable();

            // rates and percentages
            $table->decimal('failed_rate', 5, 2)->nullable();
            $table->decimal('success_rate', 5, 2)->nullable();

            // references to detailed KPI tables
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->unsignedBigInteger('trips_id')->nullable();
            $table->unsignedBigInteger('successful_deliveries_id')->nullable();
            $table->unsignedBigInteger('failed_deliveries_id')->nullable();

            // history metadata
            $table->enum('update_type', ['Create', 'Update', 'Delete'])->default('Update');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('hub_id')->references('id')->on('hub')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendance')->onDelete('set null');
            $table->foreign('trips_id')->references('id')->on('trips')->onDelete('set null');
            $table->foreign('successful_deliveries_id')->references('id')->on('successful_deliveries')->onDelete('set null');
            $table->foreign('failed_deliveries_id')->references('id')->on('failed_deliveries')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_reports_history');
    }
};
