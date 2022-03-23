<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ContactsTestsPivotTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_test_case', function (Blueprint $table) {
            $table->bigInteger('contact_id')->default(1);
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');

            $table->bigInteger('test_case_id')->default(1);
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_test_case');
    }
}
