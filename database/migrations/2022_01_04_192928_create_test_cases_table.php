<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group');
            $table->string('env');
            $table->string('class_name')->nullable();
            $table->string('filter_name')->nullable();
            $table->string('url')->nullable();
            $table->string('cmd')->nullable();
            $table->boolean('dusk_test')->default(false);
            $table->smallInteger('attempt')->default(0);
            $table->string('status')->default('Fail');
            $table->text('response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_cases');
    }
}
