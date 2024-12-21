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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('ranqueamento_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->double('nota')->nullable();
            $table->integer('codhab_jupiterweb')->nullable();
            $table->integer('prioridade_eleita')->nullable();
            $table->unsignedBigInteger('hab_id_eleita')->nullable();
            $table->foreign('hab_id_eleita')->references('id')->on('habs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
