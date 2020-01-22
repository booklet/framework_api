<?php
require_once 'tests/fixtures/models/FWTestModelUser.php';
require_once 'tests/fixtures/validator/TesterParentModel.php';
require_once 'tests/fixtures/validator/TesterChildModel.php';

class ModelPaginateTraitTest extends \CustomPHPUnitTestCase
{
    public function populateUserTable()
    {
        for ($i = 1; $i < 101; ++$i) {
            $role = $i > 50 ? 'customer_service' : 'admin';

            $user = new FWTestModelUser([
                'username' => 'Uzytkownik nr ' . $i,
                'email' => 'user' . $i . '@booklet.pl',
                'role' => $role,
                'password' => 'password1',
                'password_confirmation' => 'password1', ]);
            $user->save();
        }
    }

    public function testAllWithPaginate()
    {
        $this->populateUserTable();
        $all_users = FWTestModelUser::all();

        $this->assertEquals(count($all_users), 100);

        $params = ['page' => 3];
        list($users, $paginate_data) = FWTestModelUser::allWithPaginate(['order' => 'id DESC'], $params);

        $this->assertEquals(count($users), 30);
        $this->assertEquals($paginate_data, [
            'total_pages' => 4,
            'current_page' => 3,
            'total_items' => 100,
            'items_per_page' => 30,
        ]);

        $params = ['page' => 4];
        list($users, $paginate_data) = FWTestModelUser::allWithPaginate(['order' => 'id DESC'], $params);

        $this->assertEquals(count($users), 10);
        $this->assertEquals($paginate_data, [
            'total_pages' => 4,
            'current_page' => 4,
            'total_items' => 100,
            'items_per_page' => 30,
        ]);

        $params = ['page' => 99];
        list($users, $paginate_data) = FWTestModelUser::allWithPaginate(['order' => 'id DESC'], $params);

        $this->assertEquals(count($users), 0);
        $this->assertEquals($paginate_data, [
            'total_pages' => 4,
            'current_page' => 99,
            'total_items' => 100,
            'items_per_page' => 30,
        ]);
    }

    public function testWhereWithPaginate()
    {
        $this->populateUserTable();
        $all_users = FWTestModelUser::all(['order' => 'id DESC']);

        $this->assertEquals(count($all_users), 100);

        $params = ['page' => 2];
        list($users, $paginate_data) = FWTestModelUser::whereWithPaginate('role = ?', ['role' => 'admin'], [], $params);

        $this->assertEquals(count($users), 20);
        $this->assertEquals($paginate_data, [
            'total_pages' => 2,
            'current_page' => 2,
            'total_items' => 50,
            'items_per_page' => 30,
        ]);

        $params = ['page' => 99];
        list($users, $paginate_data) = FWTestModelUser::whereWithPaginate('role = ?', ['role' => 'admin'], [], $params);

        $this->assertEquals(count($users), 0);
        $this->assertEquals($paginate_data, [
            'total_pages' => 2,
            'current_page' => 99,
            'total_items' => 50,
            'items_per_page' => 30,
        ]);
    }

    public function testAllWithPaginateZeroResults()
    {
        $all_users = FWTestModelUser::all();

        $this->assertEquals(count($all_users), 0);

        $params = ['page' => 1];
        list($users, $paginate_data) = FWTestModelUser::allWithPaginate(['order' => 'id DESC'], $params);

        $this->assertEquals(count($users), 0);
        $this->assertEquals($paginate_data, [
            'total_pages' => 0,
            'current_page' => 1,
            'total_items' => 0,
            'items_per_page' => 30,
        ]);
    }

    public function testRelationPagination()
    {
        $parent = new TesterParentModel(['name' => 'parent item']);
        $parent->save();

        for ($i = 1; $i < 101; ++$i) {
            $child = new TesterChildModel([
                'tester_parent_model_id' => $parent->id,
                'address' => 'user' . $i . '@booklet.pl',
            ]);
            $child->save();
        }
        $all_childs = TesterChildModel::all();

        $this->assertEquals(count($all_childs), 100);
        $this->assertEquals(count($parent->childs()), 100);

        $params = ['page' => 1];

        list($childs, $paginate_data) = $parent->childsWithPaginate(['order' => 'id DESC'], $params);

        $this->assertEquals(count($childs), 25);
        $this->assertEquals($paginate_data, [
            'total_pages' => 4,
            'current_page' => 1,
            'total_items' => 100,
            'items_per_page' => 25,
        ]);
    }
}
