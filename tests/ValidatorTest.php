<?php
require_once 'tests/fixtures/models/FWTestModelUser.php';
require_once 'tests/fixtures/models/FWTestCustomModel.php';

class ValidatorTest extends \CustomPHPUnitTestCase
{
    public function testValidRequired()
    {
        $obj = new stdClass();
        $obj->name = 'Name';

        $rules = ['name' => ['required']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->name = null;
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['name'][0], 'is required.');

        // test fix
        $obj->name = 'ok name';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->name = '';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['name'][0], 'is required.'); // TODO nie $valid a obiekt
    }

    public function testValidAllowNull()
    {
        $obj = new stdClass();
        $obj->quanity = null;
        $rules = ['quanity' => ['greater_than_or_equal_to:2:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 10;
        $rules = ['quanity' => ['greater_than_or_equal_to:2:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 1;
        $rules = ['quanity' => ['greater_than_or_equal_to:2:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);

        $obj->quanity = '';
        $rules = ['quanity' => ['greater_than_or_equal_to:2:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);

        $obj->quanity = null;
        $rules = ['quanity' => ['greater_than_or_equal_to:2:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 0;
        $rules = ['quanity' => ['greater_than_or_equal_to:0:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 0;
        $rules = ['quanity' => ['greater_than_or_equal_to:1:allow_null']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
    }

    public function testValidAllowEmpty()
    {
        $obj = new stdClass();
        $obj->email = null;
        $rules = ['email' => ['email:allow_empty']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals(true, $valid->isValid());

        $obj->email = '';
        $rules = ['email' => ['email:allow_empty']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals(true, $valid->isValid());

        $obj->email = '  ';
        $rules = ['email' => ['email:allow_empty']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals(false, $valid->isValid());

        $obj->email = 'emial@email.pl';
        $rules = ['email' => ['email:empty']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals(true, $valid->isValid());

        $obj->email = 'emial@email';
        $rules = ['email' => ['email:empty']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals(false, $valid->isValid());
    }

    public function testValidTypeStringText()
    {
        $obj = new stdClass();
        $obj->name = 'Name';

        $rules = ['name' => ['type:string']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->name = 12345;
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['name'][0], 'is not string type.');
    }

    public function testValidTypeInteger()
    {
        $obj = new stdClass();
        $obj->parent_id = 1;

        $rules = ['parent_id' => ['type:integer']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->parent_id = '12345';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->parent_id = 'onetwo';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['parent_id'][0], 'is not integer type.');
    }

    public function testValidTypeDouble()
    {
        $obj = new stdClass();
        $obj->price = 99.99;

        $rules = ['price' => ['type:double']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->price = '99.99';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->price = 'onetwo';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['price'][0], 'is not double type.');
    }

    public function testValidTypeDatetime()
    {
        $obj = new stdClass();
        $obj->created_at = '2016-10-14 11:09:29';

        $rules = ['created_at' => ['type:datetime']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->created_at = '2016-10-14';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['created_at'][0], 'is not datetime type.');
    }

    public function testValidMaxLength()
    {
        $obj = new stdClass();
        $obj->name = '12345678901234567890';

        $rules = ['name' => ['max_length:20']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->name = '12345678901';
        $rules = ['name' => ['max_length:10']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['name'][0], 'is too long (max 10).');
    }

    public function testValidMaxLengthEmoji()
    {
        $obj = new stdClass();
        $obj->name = '👍🏿✌😍🇺🇸';

        // success
        $rules = ['name' => ['max_length:6']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $rules = ['name' => ['max_length:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
    }

    public function testValidGreaterThanOrEqualTo()
    {
        $obj = new stdClass();
        $obj->quanity = 6;
        $rules = ['quanity' => ['greater_than_or_equal_to:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 5;
        $rules = ['quanity' => ['greater_than_or_equal_to:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->quanity = 4;
        $rules = ['quanity' => ['greater_than_or_equal_to:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['quanity'][0], 'is low value (min 5).');

        $obj->quanity = 0.05;
        $rules = ['quanity' => ['greater_than_or_equal_to:0.01']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 0.001;
        $rules = ['quanity' => ['greater_than_or_equal_to:0.01']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['quanity'][0], 'is low value (min 0.01).');

        $obj->quanity = 0;
        $rules = ['quanity' => ['greater_than_or_equal_to:0.01']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['quanity'][0], 'is low value (min 0.01).');
    }

    public function testValidLessThanOrEqualTo()
    {
        $obj = new stdClass();
        $obj->quanity = 4;
        $rules = ['quanity' => ['less_than_or_equal_to:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->quanity = 5;
        $rules = ['quanity' => ['less_than_or_equal_to:5']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->quanity = 5;
        $rules = ['quanity' => ['less_than_or_equal_to:4']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['quanity'][0], 'is to high value (max 4).');
    }

    public function testValidEmail()
    {
        $obj = new stdClass();
        $rules = ['user_email' => ['email']];

        $valid_emails = [
            'name.lastname@domain.com',
            'a@bar.com',
            'a-b@bar.com',
            '"Abc\@def"@example.com',
            '"Joe\Blow"@example.com',
            '"Abc@def"@example.com',
            'customer/department=shipping@example.com',
            '$A12345@example.com',
            '!def!xyz%abc@example.com',
            'kamil@blacklight.digital',
            'simple@example.com',
            'very.common@example.com',
            'disposable.style.email.with+symbol@example.com',
            'other.email-with-hyphen@example.com',
            'fully-qualified-domain@example.com',
            'x@example.com',
            '#!$%&\'*+-/=?^_`{}|~@example.org',
            '_somename@example.com',
            'user%uucp!path@somehost.edu',
            '"very.(),:;<>[]\".VERY.\"very@\\ \"very\".unusual"@strange.example.com',
            'example-indeed@strange-example.com',
            'example@s.example',
            'valid@special.museum',
            '+@b.com',
            'a@b.co-foo.uk',
            'aaa@[123.123.123.123]',
            // Theoretically correct but they do not work here
            // '"hello my name is"@stutter.com',
            // '"Test \"Fail\" Ing"@example.com',
            // 'user@[2001:DB8::1]',
            // 'admin@mailserver1',
            // 'user.name+tag+sorting@example.com ',
            // '"()<>[]:,;@\\\"!#$%&\'-/=?^_`{}| ~.a"@example.org',
            // 'Test \ Folding \ Whitespace@example.com',
            // 'HM2Kinsists@(that comments are allowed)this.is.ok',
        ];

        foreach ($valid_emails as $email) {
            $obj->user_email = $email;
            $valid = new Validator($obj, $rules);

            $this->assertEquals($valid->isValid(), true);
        }

        $invalid_emails = [
            '.@',
            'a@b',
            '@bar.com',
            '@@bar.com',
            'aaa.com',
            'aaa@.com',
            'aaa@.123',
            'aaa@[123.123.123.123]a',
            'aaa@[123.123.123.333]',
            'a@bar.com.',
            'a@bar',
            'a@-b.com',
            'a@b-.com',
            '-@..com',
            '-@a..com',
            'invalid@special.museum-',
            'test@...........com',
            'foobar@192.168.0.1',
            'Abc.example.com', // (no @ character)
            'A@b@c@example.com', // (only one @ is allowed outside quotation marks)
            'a"b(c)d,e:f;g<h>i[j\k]l@example.com', // (none of the special characters in this local-part are allowed outside quotation marks)
            'just"not"right@example.com', // (quoted strings must be dot separated or the only element making up the local-part)
            'this is"not\allowed@example.com', // (spaces, quotes, and backslashes may only exist when within quoted strings and preceded by a backslash)
            'this\ still\"not\\allowed@example.com', // (even if escaped (preceded by a backslash), spaces, quotes, and backslashes must still be contained by quotes)
            '1234567890123456789012345678901234567890123456789012345678901234+x@example.com', // (local part is longer than 64 characters)
            'john..doe@example.com', // (double dot before @)
            'john.doe@example..com', // (double dot after @)
            '" "@example.org', // (space between the quotes)[clarification needed]
            // Theoretically incorrect but they do work here
            // 'shaitan@my-domain.thisisminekthx',
            // '+@b.c',
        ];

        foreach ($invalid_emails as $email) {
            $obj->user_email = $email;
            $valid = new Validator($obj, $rules);

            $this->assertEquals($valid->isValid(), false);
        }
    }

    //    public function testValidUnique()
    //    {
    //        // how test uniques witout use database
    //        $this->pending();
    //        $user1 = UserFactory::user();
    //        $user1->save();
    //
    //        $rules = ['username' => ['unique']];
    //
    //        // success valid save object?
    //        $valid = new Validator($user1, $rules);
    //
    //        $this->assertEquals($valid->isValid(), true);
    //
    //        $user2 = UserFactory::user();
    //        $valid = new Validator($user2, $rules);
    //
    //        $this->assertEquals($valid->isValid(), false);
    //        $this->assertEquals($user2->errors['username'][0], 'is not unique.');
    //    }

    public function testValidIn()
    {
        $obj = new stdClass();
        $rules = ['role' => ['in:admin,customer_service,production_worker,web,client,agency']];

        // success
        $obj->role = 'customer_service';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->role = 'xxx';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
    }

    public function testValidPassword()
    {
        $user = new FWTestModelUser(['username' => 'Name', 'email' => 'test@booklet.pl', 'role' => 'admin', 'password' => '', 'password_confirmation' => '']);
        $user->isValid();
        $this->assertEquals($user->errors['password'][0], 'cannot be blank.');

        $user = new FWTestModelUser(['username' => 'Name', 'email' => 'test@booklet.pl', 'role' => 'admin', 'password' => 'haslo', 'password_confirmation' => '']);
        $user->isValid();
        $this->assertEquals($user->errors['password'][0], 'confirmation cannot be blank.');
        $this->assertEquals($user->errors['password'][1], 'confirmation doesn\'t match.');

        $user = new FWTestModelUser(['username' => 'Name', 'email' => 'test@booklet.pl', 'role' => 'admin', 'password' => 'haslo', 'password_confirmation' => 'HASLO']);
        $user->isValid();
        $this->assertEquals($user->errors['password'][0], 'confirmation doesn\'t match.');

        $user = new FWTestModelUser(['username' => 'Name', 'email' => 'test@booklet.pl', 'role' => 'admin', 'password' => 'haslo', 'password_confirmation' => 'haslo']);
        $this->assertEquals($user->isValid(), true);
        $this->assertEquals(strlen($user->password_digest), 40);
    }

    public function testValidZipCode()
    {
        $obj = new stdClass();
        $obj->zip = '00-123';
        $rules = ['zip' => ['zip_code']];
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->zip = '00123';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->zip = '000';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['zip'][0], 'is not zip code.');

        $obj->zip = 12345;
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);
    }

    public function testValidTypeBoolean()
    {
        $obj = new stdClass();
        $obj->bool_field = 1;

        $rules = ['bool_field' => ['type:boolean']];

        // success
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        $obj->bool_field = 0;
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);

        // error
        $obj->bool_field = '0';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), true);
        //$this->assertEquals($valid->errors()['bool_field'][0], 'is not boolean type.');

        $obj->bool_field = 'true';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['bool_field'][0], 'is not boolean type.');

        $obj->bool_field = 'yes';
        $valid = new Validator($obj, $rules);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['bool_field'][0], 'is not boolean type.');
    }

    public function testCustomValidators()
    {
        $test_calas = new FWTestCustomModel();
        $test_calas->variable = 0;

        // success
        $valid = new Validator($test_calas, []);

        $this->assertEquals($valid->isValid(), false);
        $this->assertEquals($valid->errors()['variable'][0], 'must be greater than 0.');
    }
}
