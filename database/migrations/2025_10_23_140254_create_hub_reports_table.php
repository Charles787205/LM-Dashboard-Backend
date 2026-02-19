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
        Schema::create('hub_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hub_id');
            $table->unsignedBigInteger('user_id');

            // counts
            $table->integer('inbound')->default(0);
            $table->integer('outbound')->default(0);
            $table->integer('delivered')->default(0);
            $table->integer('backlogs')->default(0);
            $table->integer('failed')->default(0);
            $table->integer('misroutes')->default(0);

            // metrics / identifiers
            $table->date('date');
            $table->decimal('sdod', 8, 2)->default(0);

            // rates and percentages
            $table->decimal('failed_rate', 5, 2)->nullable();
            $table->decimal('success_rate', 5, 2)->nullable();

            $table->timestamps();

            // indexes & foreign keys
            $table->unique(['hub_id', 'date'], 'hub_reports_hub_date_unique');
            $table->foreign('hub_id')->references('id')->on('hub')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_reports');
    }
};
