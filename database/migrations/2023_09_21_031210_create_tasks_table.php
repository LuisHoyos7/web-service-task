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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->dateTime('due_date')->nullable();
            $table->string('attachments')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('state_id');
            $table->timestamps();

            // Definir la clave foránea con la tabla users
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('priority_id')->references('id')->on('priorities');
            $table->foreign('state_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
