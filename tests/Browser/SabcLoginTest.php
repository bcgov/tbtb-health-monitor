<?php

namespace Tests\Browser;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestCase;
use App\Models\TestCase as TbtbTest;

class SabcLoginTest extends DuskTestCase
{
    /**
     * Access SABC login page.
     *
     * @return void
     */
    public function testProdSabcAccessLoginPage()
    {
        $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');

            $browser->visit('https://studentaidbc.ca/dashboard/login');
            $browser->screenshot(rand() . "-testProdSabcAccessLoginPage-screenshot");
            $browser->press('Option 2 - Login with a StudentAid BC User ID');
            $browser->waitFor('input[id="user_id"]', 5);
            $browser->type('input[id="user_id"]', env('PROD_SABC_ACCESS_LOGIN_USERID'));
            $browser->type('input[id="password"]', env('PROD_SABC_ACCESS_LOGIN_PASS'));
            $browser->press('Login with StudentAid BC User ID');
            $browser->waitFor("div[id='myTabContent']", 5);
            $browser->screenshot(rand() . "-testPrdAccessLoginPage-screenshot");
            $test = $browser->driver->getPageSource();

        });
    }

    public function testUatSabcAccessLoginPage()
    {
        $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');
            $browser->visit('https://uat.studentaidbc.ca/dashboard/login');
            $browser->screenshot(rand() . "-testUatAccessLoginPage-screenshot");
            $browser->press('Option 2 - Login with a StudentAid BC User ID');
            $browser->type('input[id="user_id"]', env('UAT_SABC_ACCESS_LOGIN_USERID'));
            $browser->type('input[id="password"]', env('UAT_SABC_ACCESS_LOGIN_PASS'));
            $browser->press('Login with StudentAid BC User ID');
            $browser->waitFor("div[id='myTabContent']", 5);
            $browser->screenshot(rand() . "-testUatAccessLoginPage-screenshot");
            $test = $browser->driver->getPageSource();

        });
    }

    public function testDevSabcAccessLoginPage()
    {
        $browse = $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');
            $browser->visit('https://dev.studentaidbc.ca/dashboard/login');
            $browser->screenshot(rand() . "-testDevAccessLoginPage-screenshot");
            $browser->press('Option 2 - Login with a StudentAid BC User ID');
            $browser->type('input[id="user_id"]', env('DEV_SABC_ACCESS_LOGIN_USERID'));
            $browser->type('input[id="password"]', env('DEV_SABC_ACCESS_LOGIN_PASS'));
            $browser->press('Login with StudentAid BC User ID');
            $browser->waitFor("div[id='myTabContent']", 5);
            $browser->screenshot(rand() . "-testDevAccessLoginPage-screenshot");
            $test = $browser->driver->getPageSource();

        });
        //echo "RESULT:";
        //var_dump($browse);
    }

    public function testProdSabcCdsLoginPage()
    {
        $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');

            $browser->visit('https://studentaidbc.ca/ords/cds/cds_html_main_pkg.cds');
            $browser->screenshot(rand() . "-testProdSabcCdsLoginPage-screenshot");

            $browser->driver->switchTo()->frame(6);

            $browser->clickAtXPath('//*[@id="bNext"]');
            //$browser->waitFor('form[name="data_form"]', 5);
            $browser->screenshot(rand() . "-testProdSabcCdsLoginPage-screenshot");
            $browser->assertSee('Login Form');
            $test = $browser->driver->getPageSource();

        });
    }

    public function testUatSabcCdsLoginPage()
    {
        $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');

            $browser->visit('https://uat.studentaidbc.ca/ords/cds/cds_html_main_pkg.cds');
            $browser->screenshot(rand() . "-testUatSabcCdsLoginPage-screenshot");

            $browser->driver->switchTo()->frame(6);

            $browser->clickAtXPath('//*[@id="bNext"]');
            //$browser->waitFor('form[name="data_form"]', 5);
            $browser->screenshot(rand() . "-testUatSabcCdsLoginPage-screenshot");
            $browser->assertSee('Login Form');
            $test = $browser->driver->getPageSource();

        });
    }

    public function testDevSabcCdsLoginPage()
    {
        $this->browse(function (Browser $browser) {
            //$browser->resize('1960', '1500');

            $browser->visit('https://dev.studentaidbc.ca/ords/cds/cds_html_main_pkg.cds');
            $browser->screenshot(rand() . "-testDevSabcCdsLoginPage-screenshot");

            $browser->driver->switchTo()->frame(6);

            $browser->clickAtXPath('//*[@id="bNext"]');
            //$browser->waitFor('form[name="data_form"]', 5);
            $browser->screenshot(rand() . "-testDevSabcCdsLoginPage-screenshot");
            $browser->assertSee('Login Form');
            $test = $browser->driver->getPageSource();

        });
    }

}
