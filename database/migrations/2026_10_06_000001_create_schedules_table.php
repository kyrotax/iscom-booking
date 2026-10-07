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
            $table->string('day_name'); // e.g. Kamis
            $table->date('schedule_date'); // e.g. 2024-10-24
            $table->string('time_slot'); // e.g. 13:30 - 15:30 WIB
            $table->string('mentor_names'); // e.g. Kak Aditya W. & Kak Fadhil R.
            $table->string('location'); // e.g. Lab Komputer C301
            $table->string('topic'); // e.g. Basis Data & Web
            $table->integer('quota')->default(10); // Kapasitas maksimal peserta
            $table->integer('booked_count')->default(0); // Jumlah peserta terdaftar
            $table->string('status')->default('tersedia'); // 'tersedia', 'hampir_penuh', 'penuh', 'selesai'
            $table->timestamps();
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
