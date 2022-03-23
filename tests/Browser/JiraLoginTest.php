<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\TestCase;

class JiraLoginTest extends DuskTestCase
{
    /**
     * Access JIRA login page.
     *
     * @return void
     */
    public function testProdJiraLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://hive.aved.gov.bc.ca/jira/secure/Dashboard.jspa');
            $browser->screenshot(rand() . "-testProdJiraLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

    public function testDevJiraLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://dev.hive.aved.gov.bc.ca/jiradev/secure/Dashboard.jspa');
            $browser->screenshot(rand() . "-testDevJiraLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

    public function testProdWikiLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://hive.aved.gov.bc.ca/wiki/login.action');
            $browser->screenshot(rand() . "-testProdWikiLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

    public function testDevWikiLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://dev.hive.aved.gov.bc.ca/wikidev/login.action');
            $browser->screenshot(rand() . "-testDevWikiLoginPage-screenshot");
            $test = $browser->driver->getPageSource();
        });
    }

}
