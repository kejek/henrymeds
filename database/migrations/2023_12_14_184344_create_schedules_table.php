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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->dateTimeTz('start_time', 0);
            $table->dateTimeTz('end_time', 0);
            $table->boolean('filled')->default(false);
            $table->timestamps();
            $table->foreign('provider_id')->references('id')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
