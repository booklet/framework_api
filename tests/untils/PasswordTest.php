<?php
class PasswordTest extends \CustomPHPUnitTestCase
{
    public function testEncrypt()
    {
        $password_digest = StringUntils::encryptPassword('my_password');
        $this->assertEquals($password_digest, '393ddeab73fa104ecf2c36f7f6e5dad72237df35');
    }
}
