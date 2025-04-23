<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('problems', function (Blueprint $table) {
            // Update the ENUM to include 'unsolvable'
            $table->enum('status', ['open', 'assigned', 'resolved', 'unsolvable'])
                  ->default('open')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('problems', function (Blueprint $table) {
            // Revert to original ENUM values
            $table->enum('status', ['open', 'assigned', 'resolved'])
                  ->default('open')
                  ->change();
        });
    }
};