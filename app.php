#!/usr/bin/php
<?php
require_once 'vendor/autoload.php';

Config::set('password_salt', '18ac6e5a5e2e5567a1580f78eb33c3160bc58b0a522bad40465a918bd4ba9d5b465ffd0b1fbd');
Config::set('env', 'test');
Config::set('migrations_path', 'tests/fixtures/migrations');

$test = [];
$test['host'] = 'db';
$test['user'] = 'test_framework';
$test['password'] = 'test_framework';
$test['name'] = 'test_framework';
Config::set('db_test', $test);

$task = new CLITask($argv);

// Database tasks
// =============================================================================
// recreate test database
// $ app db:prepare
if ($task->action == 'db:prepare') {
    $task->dbPrepare();
}

// Tests tasks
// =============================================================================
// run all tests
// $ app test:all
if ($task->action == 'test:all') {
    $task->testRunAll();
}

// run specific test
// $ app test:single OrdersRequestsTest:testIndex
if ($task->action == 'test:single') {
    $task->testRunSingle();
}
