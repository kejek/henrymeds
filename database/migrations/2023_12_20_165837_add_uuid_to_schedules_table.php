<?php

use App\Models\Schedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->uuid()->after('id');
        });

        $schedules = Schedule::all();

        foreach($schedules as $schedule) {
            $schedule->uuid = Str::uuid()->toString();

            $schedule->save();
        }

        Schema::table('schedules', function (Blueprint $table) {
            $table->uuid()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
