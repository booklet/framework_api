#!/usr/bin/php
<?php
// Autoloader basic load
// =============================================================================
require_once 'config/autoload.php';
require_once 'config/application.php';

$task = new CLITask($argv);

// Info
// =============================================================================
// `app` command is alias to `php app.php` / alias app='php app.php'
// if not params, CLITask run as default all test

// Database tasks
// =============================================================================
// migrate database (works for development and production database)
// $ app db:migrate
if ($task->action == 'db:migrate') {
    $task->dbMigrate();
}

// undo last database migration (works for development and production database)
// $ app db:rollback
if ($task->action == 'db:rollback') {
    $task->dbRollback();
}

// seed data in dev database
// $ app db:seed
if ($task->action == 'db:seed') {
    $task->dbSeed();
}

// recreate test database
// $ app db:prepare
if ($task->action == 'db:prepare') {
    $task->dbPrepare();
}

// Assets tasks
// =============================================================================
// compile assets (scss to css) to production files
// $ app assets:compile
if ($task->action == 'assets:compile') {
    $task->assetsCompile();
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
