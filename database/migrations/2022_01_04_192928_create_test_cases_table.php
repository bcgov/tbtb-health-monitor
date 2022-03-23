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
        /*
         *
1,E-Services REST Services,SABC,production,,,/service-check/sabc/production/E_Services_REST_Services,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
2,AEM Applications 20-21,SABC,production,,,/service-check/sabc/production/AEM_Applications_20_21,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
3,AEM Applications 21-22,SABC,production,,,/service-check/sabc/production/AEM_Applications_21_22,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
4,Postgres Database Connection,SABC,production,,,/service-check/sabc/production/Postgres_Database_Connection,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
5,Dashboard Success Login,SABC,production,,,/service-check/sabc/production/Dashboard_Success_Login,,true,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
6,Dashboard Login HTML page,SABC,production,,,/service-check/sabc/production/Dashboard_Login_HTML_page,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
7,E-Services REST Services,SABC,dev,,,/service-check/sabc/dev/E_Services_REST_Services,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
8,AEM Applications 20-21,SABC,dev,,,/service-check/sabc/dev/AEM_Applications_20_21,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
9,AEM Applications 21-22,SABC,dev,,,/service-check/sabc/dev/AEM_Applications_21_22,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
10,Postgres Database Connection,SABC,dev,,,/service-check/sabc/dev/Postgres_Database_Connection,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
11,Dashboard Login HTML page,SABC,dev,,,/service-check/sabc/dev/Dashboard_Login_HTML_page,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
12,Dashboard Success Login,SABC,dev,,,/service-check/sabc/dev/Dashboard_Success_Login,,true,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
13,E-Services REST Services,SABC,uat,,,/service-check/sabc/uat/E_Services_REST_Services,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
14,AEM Applications 20-21,SABC,uat,,,/service-check/sabc/uat/AEM_Applications_20_21,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
15,AEM Applications 21-22,SABC,uat,,,/service-check/sabc/uat/AEM_Applications_21_22,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
16,Postgres Database Connection,SABC,uat,,,/service-check/sabc/uat/Postgres_Database_Connection,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
17,Dashboard Login HTML page,SABC,uat,,,/service-check/sabc/uat/Dashboard_Login_HTML_page,,false,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29
18,Dashboard Success Login,SABC,uat,,,/service-check/sabc/uat/Dashboard_Success_Login,,true,0,Fail,,2022-01-04 11:53:29,2022-01-04 11:53:29


         */
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
