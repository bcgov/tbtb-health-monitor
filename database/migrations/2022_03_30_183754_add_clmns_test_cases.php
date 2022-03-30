<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClmnsTestCases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->string('test_type')->after('dusk_test')->nullable();
            $table->string('assert_text')->after('attempt_total')->nullable();
            $table->text('post_data')->after('assert_text')->nullable();
            $table->text('url')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->string('url')->change();
            $table->dropColumn('test_type');
            $table->dropColumn('post_data');
            $table->dropColumn('assert_text');
        });
    }
}
