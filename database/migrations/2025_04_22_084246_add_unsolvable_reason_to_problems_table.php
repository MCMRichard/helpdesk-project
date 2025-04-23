<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->text('unsolvable_reason')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn('unsolvable_reason');
        });
    }
};