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
        Schema::create('habs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('ranqueamento_id')->constrained();
            $table->integer('codhab');
            $table->string('nomhab');
            $table->string('perhab');
            $table->boolean('permite_ambos_periodos')->default(0);
            $table->integer('vagas')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habs');
    }
};
