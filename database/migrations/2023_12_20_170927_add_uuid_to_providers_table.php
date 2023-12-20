<?php

use App\Models\Provider;
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
        Schema::table('providers', function (Blueprint $table) {
            $table->uuid()->after('id');
        });

        $providers = Provider::all();

        foreach($providers as $provider) {
            $provider->uuid = Str::uuid()->toString();

            $provider->save();
        }

        Schema::table('providers', function (Blueprint $table) {
            $table->uuid()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
