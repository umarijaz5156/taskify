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
        Schema::create('task_chats', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->integer('client_id')->nullable();
            $table->text('message')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_chats');
    }
};
