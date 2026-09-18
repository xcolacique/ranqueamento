<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * Teste de login senha unica faker.
     */
    public function test_login(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://ranqueamento/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->waitForText('Ranqueamento')
                ->assertSee('Sair');
        });
    }
}
