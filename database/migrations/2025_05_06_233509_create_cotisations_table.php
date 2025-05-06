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
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id'); // participant
            $table->decimal('montant', 10, 2);
            $table->string('preuve_path')->nullable();
            $table->date('date_versement');
            $table->unsignedBigInteger('created_by'); // admin ayant enregistré
            $table->timestamps();
            $table->softDeletes();
        
            $table->index('group_id');
            $table->index('user_id');
            $table->index('created_by');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
