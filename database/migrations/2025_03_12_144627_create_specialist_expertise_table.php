<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialist_expertise', function (Blueprint $table) {
            $table->unsignedBigInteger('specialist_id');
            $table->unsignedBigInteger('problem_type_id');
            $table->primary(['specialist_id', 'problem_type_id']);
            $table->foreign('specialist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('problem_type_id')->references('problem_type_id')->on('problem_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_expertise');
    }
};