<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop the existing availability column
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('availability');
        });

        // Add a new available_until date column
        Schema::table('annonces', function (Blueprint $table) {
            $table->date('available_until')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        // Reverse the changes: drop available_until and re-add availability as JSON
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('available_until');
        });

        Schema::table('annonces', function (Blueprint $table) {
            $table->json('availability')->after('image');
        });
    }
};