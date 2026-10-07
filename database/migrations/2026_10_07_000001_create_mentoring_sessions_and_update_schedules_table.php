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
        Schema::create('mentoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('badge_label')->default('Sesi Terbuka');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('mentoring_session_id')->nullable()->after('id')->constrained('mentoring_sessions')->onDelete('cascade');
            $table->time('start_time')->nullable()->after('time_slot');
            $table->time('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['mentoring_session_id']);
            $table->dropColumn(['mentoring_session_id', 'start_time', 'end_time']);
        });

        Schema::dropIfExists('mentoring_sessions');
    }
};
