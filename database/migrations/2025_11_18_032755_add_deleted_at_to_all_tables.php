<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('software', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('penginstalans', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('perbaikans', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('rekaps', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('software', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penginstalans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('perbaikans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
