<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestCase;
use App\Models\TestCase as TbtbTest;

class PtibLoginTest extends DuskTestCase
{
    /**
     * Access PTIB login page.
     *
     * @return void
     */
    public function testProdPtipAdminLoginPage()
    {
        $this->browse(function (Browser $browser) {
//            $browser->visit('https://logon.gov.bc.ca/clp-cgi/int/logon.cgi?TARGET=https://admin.privatetraininginstitutions.gov.bc.ca/&flags=1100:1,7&toggle=1');
            $browser->visit('https://logon7.gov.bc.ca/clp-cgi/int/logon.cgi?TARGET=https://admin.privatetraininginstitutions.gov.bc.ca/&flags=1100:1,7&toggle=1');
            $browser->screenshot(time() . "-testProdPtipAdminLoginPage-screenshot");
            $browser->type('input[id="user"]', env('PROD_PTIP_ADMIN_LOGIN_USERID'));
            $browser->type('input[id="password"]', env('PROD_PTIP_ADMIN_LOGIN_PASS'));
            $browser->press('Continue');
            $browser->screenshot(time() . "-testProdPtipAdminLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

    public function testUatPtipAdminLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://logon7.gov.bc.ca/clp-cgi/int/logon.cgi?TARGET=https://admin.privatetraininginstitutions.gov.bc.ca/&flags=1100:1,7&toggle=1');
            $browser->screenshot(time() . "-testUatPtipAdminLoginPage-screenshot");
            $browser->type('input[id="user"]', env('UAT_PTIP_ADMIN_LOGIN_USERID'));
            $browser->type('input[id="password"]', env('UAT_PTIP_ADMIN_LOGIN_PASS'));
            $browser->press('Continue');
            $browser->screenshot(time() . "-testUatPtipAdminLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

}
