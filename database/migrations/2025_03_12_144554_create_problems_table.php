<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problems', function (Blueprint $table) {
            $table->id('problem_number');
            $table->unsignedBigInteger('caller_id');
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->unsignedBigInteger('problem_type_id');
            $table->string('equipment_serial')->nullable();
            $table->unsignedBigInteger('software_id')->nullable();
            $table->enum('status', ['open', 'assigned', 'resolved']);
            $table->timestamp('reported_time');
            $table->timestamp('resolved_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('caller_id')->references('caller_id')->on('callers');
            $table->foreign('operator_id')->references('id')->on('users');
            $table->foreign('specialist_id')->references('id')->on('users');
            $table->foreign('problem_type_id')->references('problem_type_id')->on('problem_types');
            $table->foreign('equipment_serial')->references('serial_number')->on('equipment');
            $table->foreign('software_id')->references('software_id')->on('software');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};