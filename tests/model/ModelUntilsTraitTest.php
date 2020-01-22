<?php
require_once 'tests/fixtures/models/FWTestModelUser.php';

class ModelUntilsTraitTest extends \CustomPHPUnitTestCase
{
    public function testIsNewRecord()
    {
        $user = new FWTestModelUser([
            'username' => 'Uzytkownik nr1',
            'email' => 'user1@booklet.pl',
            'role' => 'admin',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);

        $this->assertEquals($user->isNewRecord(), true);

        $user->save();

        $this->assertEquals($user->isNewRecord(), false);
    }

    public function testPluralizeClassName()
    {
        $user = new FWTestModelUser([
            'username' => 'Uzytkownik nr1',
            'email' => 'user1@booklet.pl',
            'role' => 'admin',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);

        $this->assertEquals($user->PluralizeClassName(), 'FWTestModelUsers');
    }
}
