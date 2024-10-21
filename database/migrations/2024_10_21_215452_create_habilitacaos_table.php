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
        Schema::create('habilitacaos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('ranqueamento_id')->constrained();
            $table->integer('codhab');
            $table->string('nomhab');
            $table->integer('perhab');
            $table->integer('vagas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habilitacaos');
    }
};
