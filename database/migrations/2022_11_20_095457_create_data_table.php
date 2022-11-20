<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('waktu');
            $table->tinyInteger('kode_hari');
            $table->string('teras_rumah');
            $table->string('ruang_tamu');
            $table->string('kamar_utama');
            $table->string('kamar_kedua');
            $table->string('dapur');
            $table->string('toilet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data');
    }
};
