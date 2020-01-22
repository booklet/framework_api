<?php
error_reporting(E_ALL | E_STRICT);
// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install --dev\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

Config::set('password_salt', '18ac6e5a5e2e5567a1580f78eb33c3160bc58b0a522bad40465a918bd4ba9d5b465ffd0b1fbd');
Config::set('env', 'test');
Config::set('migrations_path', 'tests/fixtures/migrations');

$test = [];
$test['host'] = 'db';
$test['user'] = 'test_framework';
$test['password'] = 'test_framework';
$test['name'] = 'test_framework';
Config::set('db_test', $test);

MyDB::connect(Config::get('db_test'));

abstract class CustomPHPUnitTestCase extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $skip_clear_database = false;
        if (isset($this->skip_database_clear_before)) {
            $arr = $this->skip_database_clear_before;
            if (in_array('all', $arr)) {
                $skip_clear_database = true;
            }
            if (in_array($this->getName(), $arr)) {
                $skip_clear_database = true;
            }
        }
        if (!$skip_clear_database) {
            MyDB::clearDatabaseExceptSchema();
        }
    }

    public function request($method, $url, $token, $data = [], $options = [])
    {
        return new TesterTestRequest($method, $url, $token, $data, $options);
    }
}
