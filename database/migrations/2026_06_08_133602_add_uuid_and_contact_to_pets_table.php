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
        Schema::table('pets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('emergency_contact')->nullable()->after('coat_color');
        });

        // Populate UUID for existing pets
        foreach (\App\Models\Pet::all() as $pet) {
            $pet->uuid = \Illuminate\Support\Str::uuid();
            $pet->save();
        }

        Schema::table('pets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'emergency_contact']);
        });
    }
};
