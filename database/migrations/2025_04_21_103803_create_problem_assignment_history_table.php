<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('problem_assignment_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('problem_id');
            $table->unsignedBigInteger('specialist_id');
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('problem_id')->references('problem_number')->on('problems')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('problem_assignment_history');
    }
};