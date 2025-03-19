<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_types', function (Blueprint $table) {
            $table->id('problem_type_id');
            $table->string('name');
            $table->unsignedBigInteger('parent_type_id')->nullable();
            $table->foreign('parent_type_id')->references('problem_type_id')->on('problem_types')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_types');
    }
};