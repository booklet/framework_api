<?php
class ResponseTest extends \CustomPHPUnitTestCase
{
    public function testFnRaiseError()
    {
        $res = Response::raiseError(401, ['Missing login or password.']);
        $this->assertEquals($res, '{"errors":[{"message":"Missing login or password."}]}');

        $res = Response::raiseError(401, ['Missing login or password.', 'Other error message']);
        $this->assertEquals($res, '{"errors":[{"message":"Missing login or password."},{"message":"Other error message"}]}');
    }
}
