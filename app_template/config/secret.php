<?php
$development = [];
$development['host'] = '127.0.0.1';
$development['user'] = 'db_user';
$development['password'] = 'db_password';
$development['name'] = 'db_name';
Config::set('db_development', $development);

$test = [];
$test['host'] = '127.0.0.1';
$test['user'] = 'db_user';
$test['password'] = 'db_password';
$test['name'] = 'db_name';
Config::set('db_test', $test);
