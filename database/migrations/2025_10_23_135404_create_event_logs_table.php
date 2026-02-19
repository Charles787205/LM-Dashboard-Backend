<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EventTypeEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', EventTypeEnum::values());
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('description');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
