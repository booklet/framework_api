<?php
class BasicOrmTest extends \CustomPHPUnitTestCase
{
    public function populateUserTable()
    {
        $user1 = new FWTestModelUser(['username' => 'Uzytkownik nr1', 'email' => 'user1@booklet.pl', 'role' => 'admin', 'password' => 'password1', 'password_confirmation' => 'password1']);
        $user1->save();
        $user2 = new FWTestModelUser(['username' => 'Uzytkownik nr2', 'email' => 'user2@booklet.pl', 'role' => 'customer_service', 'password' => 'password2', 'password_confirmation' => 'password2']);
        $user2->save();
        $user3 = new FWTestModelUser(['username' => 'Uzytkownik nr3', 'email' => 'user3@booklet.pl', 'role' => 'customer_service', 'password' => 'password3', 'password_confirmation' => 'password3']);
        $user3->save();
        $user4 = new FWTestModelUser(['username' => 'Uzytkownik nr4', 'email' => 'user4@booklet.pl', 'role' => 'customer_service', 'password' => 'password4', 'password_confirmation' => 'password4']);
        $user4->save();
        $user5 = new FWTestModelUser(['username' => 'Uzytkownik nr5', 'email' => 'user5@booklet.pl', 'role' => 'admin', 'password' => 'password5', 'password_confirmation' => 'password5']);
        $user5->save();
        $user6 = new FWTestModelUser(['username' => 'Uzytkownik nr6', 'email' => 'user6@booklet.pl', 'role' => 'customer_service', 'password' => 'password6', 'password_confirmation' => 'password6']);
        $user6->save();
    }

    public function testAll()
    {
        $this->populateUserTable();
        $users = FWTestModelUser::all();

        $this->assertEquals(count($users), 6);
        $this->assertEquals($users[0]->username, 'Uzytkownik nr1');
    }

    public function testAllByOrder()
    {
        $this->populateUserTable();
        $users = FWTestModelUser::all(['order' => 'id DESC']);

        $this->assertEquals(count($users), 6);
        $this->assertEquals($users[0]->username, 'Uzytkownik nr6');
    }

    public function testAllByLimitAndPage()
    {
        $this->populateUserTable();
        $users = FWTestModelUser::all(['limit' => '2', 'page' => 2]);

        $this->assertEquals(count($users), 2);
        $this->assertEquals($users[0]->username, 'Uzytkownik nr3');
    }

    public function testFind()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::find(3);

        $this->assertEquals($user->username, 'Uzytkownik nr3');
    }

    public function testFindWithWrongId()
    {
        $this->populateUserTable();

        try {
            $user = FWTestModelUser::find(100);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "Couldn't find FWTestModelUser with id=100");
        }
    }

    public function testFindBy()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::findBy('email', 'user2@booklet.pl');

        $this->assertEquals($user->username, 'Uzytkownik nr2');
    }

    public function testFirst()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::first();
        $this->assertEquals($user->username, 'Uzytkownik nr1');
    }

    public function testFast()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::last();
        $this->assertEquals($user->username, 'Uzytkownik nr6');
    }

    public function testWhere()
    {
        $this->populateUserTable();
        $users = FWTestModelUser::where('role = ?', ['role' => 'customer_service']);

        $this->assertEquals(count($users), 4);
    }

    public function testWhereTwoWar()
    {
        $this->populateUserTable();
        $users = FWTestModelUser::where('role = ? AND username = ?', ['role' => 'customer_service', 'username' => 'Uzytkownik nr3']);

        $this->assertEquals(count($users), 1);
        $this->assertEquals($users[0]->username, 'Uzytkownik nr3');
    }

    public function testSave()
    {
        $this->markTestSkipped();
    }

    public function testUpdate()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::find(3);
        $user->update(['username' => 'Nowa nazwa', 'email' => 'nowyemail@booklet.pl']);

        $user_reload = FWTestModelUser::find(3);
        $this->assertEquals($user_reload->username, 'Nowa nazwa');
        $this->assertEquals($user_reload->email, 'nowyemail@booklet.pl');
    }

    public function testDestroy()
    {
        $this->populateUserTable();

        $user = FWTestModelUser::find(3);
        $this->assertEquals($user->destroy(), true);

        $users = FWTestModelUser::all();
        $this->assertEquals(count($users), 5);
    }

    public function testFnCreateDbObject()
    {
        $this->populateUserTable();
        $user = FWTestModelUser::first();

        $user->username = 'Nowa nazwa uzytkownia';
        $user->email = 'user101@booklet.pl';

        $orm = new MysqlORM(null, $user);
        $db_obj = MysqlORMObjectCreator::createDbObject($orm->model_obj);

        $this->assertEquals(ObjectUntils::objToArray($db_obj), ['username' => 'Nowa nazwa uzytkownia', 'email' => 'user101@booklet.pl']);

        $user->save();
        $user = FWTestModelUser::first();
        $this->assertEquals($user->username, 'Nowa nazwa uzytkownia');
        $this->assertEquals($user->email, 'user101@booklet.pl');
    }
}
