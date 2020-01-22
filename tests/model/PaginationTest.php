<?php
require_once 'tests/fixtures/models/FWTestModelUser.php';
require_once 'tests/fixtures/models/FWTestCustomModel.php';

class PaginationTest extends \CustomPHPUnitTestCase
{
    public function testPagination()
    {
        for ($i = 0; $i < 250; ++$i) {
            $user = new FWTestModelUser([
                'username' => 'user' . $i,
                'email' => 'user' . $i . '@test.com',
                'role' => 'user',
                'password' => 'none',
                'password_confirmation' => 'none',
            ]);
            $user->save();
        }

        $users = FWTestModelUser::all(['paginate' => 2]);
        $this->assertEquals(count($users), 25);
        $this->assertEquals($users[0]->id, 26);

        $users = FWTestModelUser::all(['paginate' => 2, 'per_page' => 50]);
        $this->assertEquals(count($users), 50);
        $this->assertEquals($users[0]->id, 51);

        $users = FWTestModelUser::all(['paginate' => 3, 'per_page' => 100]);
        $this->assertEquals(count($users), 50);
        $this->assertEquals($users[0]->id, 201);

        $users = FWTestModelUser::all(['paginate' => 33, 'per_page' => 100]);
        $this->assertEquals(count($users), 0);
    }
}
