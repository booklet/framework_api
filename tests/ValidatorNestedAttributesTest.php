<?php
class ValidatorNestedAttributesTest extends \CustomPHPUnitTestCase
{
    public function test()
    {
        $this->markTestSkipped();
    }

    //    // create new object with first level nested resources
//    public function testValidObjectWithNestedAttributes()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                ['address' => 'email1@test.com'],
//                ['address' => 'email2@test.com'],
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//        $valid = new Validator($parent, $parent->validationRules());
//
//        $this->assertEquals($valid->isValid(), true);
//    }
//
//    // check vaidation for nested resources (address in unique, required and not blank allowed)
//    public function testValidObjectWithNestedAttributesWithRequiredError()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                ['address' => ''],
//                ['address' => 'email1@test.com'],
//                ['address' => ''],
//                ['address' => 'email2@test.com']
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 2);
//        $this->assertEquals($parent->errors['childs[0].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[0].address'][1], 'email is not valid.');
//        $this->assertEquals($parent->errors['childs[0].address'][2], 'is not unique.');
//        $this->assertEquals($parent->errors['childs[2].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[2].address'][1], 'email is not valid.');
//        $this->assertEquals($parent->errors['childs[2].address'][2], 'is not unique.');
//    }
//
//    // add childs to exist object
//    public function testWithOneSaveObject()
//    {
//        $this->pending();
//        $child = new TesterChildModel(['address' => 'email@test.com', 'tester_parent_model_id' => 0]);
//        $child->save();
//
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                ['address' => ''],
//                ['address' => 'email@test.com'],
//                ['address' => 'email@test.com']
//            ]
//        ];
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 3);
//        $this->assertEquals($parent->errors['childs[0].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[0].address'][1], 'email is not valid.');
//        $this->assertEquals($parent->errors['childs[1].address'][0], 'is not unique.');
//        $this->assertEquals($parent->errors['childs[2].address'][0], 'is not unique.');
//    }
//
//    //
//    public function testNotValidParentObject()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'',
//            'childs_attributes' => [
//                ['address' => ''],
//                ['address' => 'email@test.com'],
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//
//        $this->assertEquals(count($parent->errors), 2);
//        $this->assertEquals($parent->errors['name'][0], 'is required.');
//
//        $this->assertEquals($parent->errors['childs[0].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[0].address'][1], 'email is not valid.');
//    }
//
//    // save with two level nested objects
//    public function testWithThreeLevelSave()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                [
//                    'address' => 'email1@test.com',
//                    'grandsons_attributes' => [
//                        ['description' => 'Lorem ipsum']
//                    ]
//                ]
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), true);
//    }
//
//    // try to save object to not belongs to parent
//    public function testSaveWithManipulateParentId()
//    {
//        $this->pending();
//
//        $parent = new TesterParentModel(['name' => 'Parent name']);
//        $parent->save();
//
//        $child1 = new TesterChildModel(['address' => 'email1@test.com', 'tester_parent_model_id' => $parent->id]);
//        $child1->save();
//
//        $child2 = new TesterChildModel(['address' => 'email2@test.com', 'tester_parent_model_id' => $parent->id]);
//        $child2->save();
//
//        $child3 = new TesterChildModel(['address' => 'email3@test.com', 'tester_parent_model_id' => 999]);
//        $child3->save();
//
//        $data = [
//            'childs_attributes' => [
//                ['id' => 1, 'address' => 'email@test.com'],
//                ['id' => 3, 'address' => 'email@test.com']
//            ]
//        ];
//
//        $parent->update($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 1);
//        $this->assertEquals($parent->errors['childs[1].id'][0], 'Item not belongs to this parent.');
//    }
//
//    // test validation on granson object (required description)
//    public function testWithThreeLevelSaveGrandsonError()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                [
//                    'address' => 'email@test.com',
//                    'grandsons_attributes' => [
//                        ['description' => '']
//                    ]
//                ]
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 1);
//        $this->assertEquals($parent->errors['childs[0].grandsons[0].description'][0], 'is required.');
//    }
//
//    // test childs validation with grandson
//    public function testWithThreeLevelSaveDubleError()
//    {
//        $this->pending();
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                [
//                    'address' => 'email@test.com',
//                    'grandsons_attributes' => [
//                        ['description' => '']
//                    ]
//                ],
//                [
//                    'address' => 'email@test.com',
//                    'grandsons_attributes' => [
//                        ['description' => 'Test']
//                    ]
//                ]
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 3);
//        $this->assertEquals($parent->errors['childs[0].address'][0], 'is not unique.');
//        $this->assertEquals($parent->errors['childs[0].grandsons[0].description'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[1].address'][0], 'is not unique.');
//    }
//
//    // update childs and add new child object
//    public function testUpdateObjectsAndAddNew()
//    {
//        $this->pending();
//
//        $child1 = new TesterChildModel(['address' => 'email1@test.com', 'tester_parent_model_id' => 1]);
//        $child1->save();
//
//        $child2 = new TesterChildModel(['address' => 'email2@test.com', 'tester_parent_model_id' => 1]);
//        $child2->save();
//
//        $child3 = new TesterChildModel(['address' => 'email3@test.com', 'tester_parent_model_id' => 1]);
//        $child3->save();
//
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                ['id' => 1, 'address' => 'new_email1@test.com'],
//                ['id' => 2, 'address' => 'new_email2@test.com'],
//                ['id' => 3, 'address' => ''],
//                ['address' => 'other-address@com.pl']
//                // ['address' => 'kontakt@com.pl']
//                // taki adress jest juz w bazie danych ale wczesniej zostal zmieniony
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//
//        $this->assertEquals($parent->isValid(), false);
//        $this->assertEquals(count($parent->errors), 1);
//        $this->assertEquals($parent->errors['childs[2].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[2].address'][1], 'email is not valid.');
//    }
//
//    // test delete validation object
//    public function testWithObjectToDelete()
//    {
//        $this->pending();
//        $child1 = new TesterChildModel(['address' => 'email@test.com', 'tester_parent_model_id' => 1]);
//        $child1->save();
//
//        $data = [
//            'name'=>'Parent name',
//            'childs_attributes' => [
//                ['id' => 1, 'address' => '', '_destroy' => '1'],
//                ['id' => 1, 'address' => '', '_destroy' => 1],
//                ['id' => 1, 'address' => '', '_destroy' => 'wrong'],
//            ]
//        ];
//
//        $parent = new TesterParentModel($data);
//        $this->assertEquals($parent->isValid(), false);
//
//        $this->assertEquals(count($parent->errors), 1);
//        $this->assertEquals($parent->errors['childs[2].address'][0], 'is required.');
//        $this->assertEquals($parent->errors['childs[2].address'][1], 'email is not valid.');
//
//    }
}
