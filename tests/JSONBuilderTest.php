<?php
class JSONBuilderTest extends \CustomPHPUnitTestCase
{
    public function testPresetPath()
    {
        $response_body = new JSONBuilder(['atrib1' => 'value1', 'atrib2' => 'value2'], 'tests/fixtures/jsonbuilder/test1.php');

        $this->assertEquals($response_body->render(), ['a' => 'value1', 'b' => 'value2']);
    }

    public function testCallFromController()
    {
        $fake_user = new stdClass();
        $fake_user->id = 1;
        $fake_user->email = 'test@booklet.pl';
        $fake_user->username = 'Nazwa uzytkownia';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class SessionController does not exist');

        $response_body = new JSONBuilder(['token' => 'TOKEN', 'user' => $fake_user], 'SessionController::create');
        $response_body->render();
    }
}
