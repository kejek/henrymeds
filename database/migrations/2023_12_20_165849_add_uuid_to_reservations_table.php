<?php

use App\Models\Reservation;
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
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid()->after('id');
        });

        $reservations = Reservation::all();

        foreach($reservations as $reservation) {
            $reservation->uuid = Str::uuid()->toString();

            $reservation->save();
        }

        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
