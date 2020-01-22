<?php
require_once 'tests/fixtures/validator/TesterParentModel.php';
require_once 'tests/fixtures/validator/TesterChildModel.php';
require_once 'tests/fixtures/validator/TesterGrandsonModel.php';

class MysqlORMNestedAttributesTest extends \CustomPHPUnitTestCase
{
    public function testSaveObjectWithNestedAttributes()
    {
        $data = [
            'name' => 'Parent name',
            'childs_attributes' => [
                ['address' => 'email1@test.com'],
                ['address' => 'email2@test.com'],
            ],
        ];

        $parent = new TesterParentModel($data);
        $parent->save();

        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->childs()), 2);

        $parent = TesterParentModel::find($parent->id);
        $this->assertEquals(count($parent->childs()), 2);

        $childs = TesterChildModel::where('tester_parent_model_id = ?', ['tester_parent_model_id' => 1]);
        $this->assertEquals(count($childs), 2);
    }

    public function testSaveObjectWithWrongNestedAttributes()
    {
        $data = [
            'name' => 'Parent name',
            'childs_attributes' => [
                ['address' => 'email1@test.com'],
                ['address' => ''],
                ['address' => 'email1@test.com'],
            ],
        ];

        $parent = new TesterParentModel($data);
        $parent->save();

        $this->assertEquals($parent->id, null);
        $this->assertEquals(count($parent->errors), 3);
    }

    public function testAddChildsToExistObjects()
    {
        $parent = new TesterParentModel(['name' => 'Parent name']);
        $parent->save();

        $child1 = new TesterChildModel(['address' => 'email1@test.com', 'tester_parent_model_id' => $parent->id]);
        $child1->save();

        $child2 = new TesterChildModel(['address' => 'email2@test.com', 'tester_parent_model_id' => $parent->id]);
        $child2->save();

        $data = [
            'id' => $parent->id,
            'name' => 'Parent name',
            'childs_attributes' => [
                ['id' => $child1->id, 'address' => 'email1@test.com'],
                ['id' => $child2->id, 'address' => 'email2@test.com'],
                ['address' => 'email3@test.com'],
            ],
        ];

        $parent->update($data);

        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->childs()), 3);

        $childs = TesterChildModel::where('tester_parent_model_id = ?', ['tester_parent_model_id' => 1]);
        $this->assertEquals(count($childs), 3);
    }

    public function testDeleteFormNestedObjects()
    {
        $parent = new TesterParentModel(['name' => 'Parent name']);
        $parent->save();

        $child1 = new TesterChildModel(['address' => 'email1@test.com', 'tester_parent_model_id' => $parent->id]);
        $child1->save();

        $child2 = new TesterChildModel(['address' => 'email2@test.com', 'tester_parent_model_id' => $parent->id]);
        $child2->save();

        $data = [
            'id' => $parent->id,
            'name' => 'Parent name',
            'childs_attributes' => [
                ['id' => $child1->id, 'address' => 'email1@test.com'],
                ['id' => $child2->id, 'address' => 'email2@test.com', '_destroy' => '1'],
            ],
        ];

        $parent->update($data);

        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->childs()), 1);

        $childs = TesterChildModel::where('tester_parent_model_id = ?', ['tester_parent_model_id' => 1]);
        $this->assertEquals(count($childs), 1);
    }

    public function testUpdateNestedObjects()
    {
        $parent = new TesterParentModel(['name' => 'Parent name']);
        $parent->save();

        $child1 = new TesterChildModel(['address' => 'email1@test.com', 'tester_parent_model_id' => $parent->id]);
        $child1->save();

        $child2 = new TesterChildModel(['address' => 'email2@test.com', 'tester_parent_model_id' => $parent->id]);
        $child2->save();

        $data = [
            'id' => $parent->id,
            'name' => 'Parent name',
            'childs_attributes' => [
                ['id' => $child1->id, 'address' => 'email1@test.com'],
                ['id' => $child2->id, 'address' => 'new_email2@test.com'],
            ],
        ];

        $parent->update($data);

        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->childs()), 2);

        $childs = TesterChildModel::where('tester_parent_model_id = ?', ['tester_parent_model_id' => 1]);
        $this->assertEquals(count($childs), 2);

        $this->assertEquals($childs[1]->address, 'new_email2@test.com');
    }

    public function testRemoveNestedAttributesWhenSaveObject()
    {
        $data = [
            'tester_parent_model_id' => 1,
            'address' => 'test1@test.com',
            'grandsons_attributes' => [
                ['description' => 'wartosc 1'],
                ['description' => 'wartosc 2'],
            ],
        ];

        $parent = new TesterChildModel($data);
        $parent->save();

        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->grandsons()), 2);

        $parent->update(['address' => 'test2@test.com']);
        $this->assertEquals($parent->id, 1);
        $this->assertEquals(count($parent->grandsons()), 2);

        $parent = TesterChildModel::find($parent->id);
        $this->assertEquals(count($parent->grandsons()), 2);
    }
}
