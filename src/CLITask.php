<?php
class CLITask
{
    public $action;
    public $action_param;

    public function __construct($args, $options = [])
    {
        $this->action = $args[1] ?? null;
        $this->action_param = $args[2] ?? null;
        $this->options = $options;
        // run tests if no action (parmas)
        if (!$this->action) {
            $this->testRunAll();
        }
    }

    public function dbMigrate()
    {
        echo "\nRun database migrations (" . Config::get('env') . ")\n";
        $runs_migrations_paths = $this->runMigrations();

        foreach ($runs_migrations_paths as $migration_path) {
            echo CLIUntils::colorizeConsoleOutput("$migration_path\n\n", 'SUCCESS');
        }

        if (empty($runs_migrations_paths)) {
            echo CLIUntils::colorizeConsoleOutput("All migrations are made\n\n", 'SUCCESS');
        }
    }

    public function dbRollback()
    {
        echo "\nRun database rollback (" . Config::get('env') . ")\n";
        $rollback_status = $this->runRollback();
        echo CLIUntils::colorizeConsoleOutput($rollback_status['message'] . "\n\n", $rollback_status['status']);
    }

    public function dbSeed()
    {
        MyDB::connect(Config::get('db_development'));
        MyDB::clearDatabaseExceptSchema();
        require_once 'db/seed.php';
        echo CLIUntils::colorizeConsoleOutput("\nSeeds database (" . Config::get('env') . ") successfully\n\n", 'SUCCESS');
    }

    public function dbPrepare()
    {
        echo "\nClear and run migrations for tests database\n";
        Config::set('env', 'test');
        $this->dropAllTablesAndRecreate();
        $this->runMigrations(['engine' => 'memory']);
        echo CLIUntils::colorizeConsoleOutput("Test database migration successfully\n\n", 'SUCCESS');
    }

    public function testRunAll()
    {
        if (isset($this->options['use_database']) and $this->options['use_database'] == false) {
            $db = null;
        } else {
            Config::set('env', 'test');
            $db_setup = 'db_' . Config::get('env');
            MyDB::connect(Config::get($db_setup));
            $db = MyDB::db();
        }

        echo "\nRun all tests\n";
        $time_start = microtime(true);
        $tests_paths = $this->options['tests_paths'] ?? ['tests'];
        $tests = new Tester(['db_connection' => $db, 'tests_paths' => $tests_paths, 'migrations_path' => MigrationTools::migrationsPath()]);
        $tests->run();
        echo "\nFinished in " . number_format((microtime(true) - $time_start), 2) . " seconds.\n\n";
    }

    public function testRunModule()
    {
        if (isset($this->options['use_database']) and $this->options['use_database'] == false) {
            $db = null;
        } else {
            Config::set('env', 'test');
            $db_setup = 'db_' . Config::get('env');
            MyDB::connect(Config::get($db_setup));
            $db = MyDB::db();
        }

        echo "\nRun module tests\n";
        $time_start = microtime(true);
        $tests_paths = [];
        $tests_paths[] = 'app/modules/' . $this->action_param;
        if (file_exists('tests/modules/' . $this->action_param)) {
            $tests_paths[] = 'tests/modules/' . $this->action_param;
        }
        $tests = new Tester(['db_connection' => $db, 'tests_paths' => $tests_paths, 'migrations_path' => MigrationTools::migrationsPath()]);
        $tests->run();
        echo "\nFinished in " . number_format((microtime(true) - $time_start), 2) . " seconds.\n\n";
    }

    // TODO refactoring this, merge with RunModule
    public function testRunPlugin()
    {
        if (isset($this->options['use_database']) and $this->options['use_database'] == false) {
            $db = null;
        } else {
            Config::set('env', 'test');
            $db_setup = 'db_' . Config::get('env');
            MyDB::connect(Config::get($db_setup));
            $db = MyDB::db();
        }

        echo "\nRun plugin tests\n";
        $time_start = microtime(true);
        $tests_paths = [];
        $tests_paths[] = 'app/plugins/' . $this->action_param;
        if (file_exists('tests/plugins/' . $this->action_param)) {
            $tests_paths[] = 'tests/plugins/' . $this->action_param;
        }
        $tests = new Tester(['db_connection' => $db, 'tests_paths' => $tests_paths, 'migrations_path' => MigrationTools::migrationsPath()]);
        $tests->run();
        echo "\nFinished in " . number_format((microtime(true) - $time_start), 2) . " seconds.\n\n";
    }

    public function testRunSingle()
    {
        Config::set('env', 'test');
        $db_setup = 'db_' . Config::get('env');
        MyDB::connect(Config::get($db_setup));

        echo "\nRun single test\n";
        $time_start = microtime(true);
        $tests_paths = $this->options['tests_paths'] ?? ['tests'];
        $tests = new Tester(['db_connection' => MyDB::db(), 'tests_paths' => $tests_paths, 'single_test_to_run' => $this->action_param, 'migrations_path' => MigrationTools::migrationsPath()]);
        $tests->run();
        echo "\nFinished in " . number_format((microtime(true) - $time_start), 2) . " seconds.\n\n";
    }

    // Zakladamy ze w aplikacji bedzie klasa AssetsCompiler z metoda compile
    // ktora obsluzy przygotowanie assetsow
    public function assetsCompile()
    {
        echo "\nRun compile assets\n";
        $time_start = microtime(true);
        $assets = new AssetsCompiler();
        $assets->compile();
        echo "\nCompiled in " . number_format((microtime(true) - $time_start), 2) . " seconds.\n\n";
    }

    // Run all not migrated migrations
    private function runMigrations()
    {
        $db_setup = 'db_' . Config::get('env');
        MyDB::connect(Config::get($db_setup));

        MigrationTools::createSchemaMigrationsTable();

        $migrations_path = MigrationTools::migrationsPath();

        $runs_migrations_arr = [];
        foreach (glob($migrations_path . '/' . MigrationTools::MIGRATION_FILE_PATTERN) as $file) {
            $version = MigrationTools::getVersionFromFilename($file);
            $is_migrated = MigrationTools::isMigratedMigration($version);

            // if not migrated, execute migration
            if ($is_migrated == false) {
                require_once $file;

                $migration_class_name = MigrationTools::getClassNameFromFilename($file);
                $query = (new $migration_class_name())->up();

                if (isset($options['engine']) and $options['engine'] == 'memory') {
                    $query = $this->convertQueryToMysqlEngineMemory($query);
                }

                $result = mysqli_query(MyDB::db(), $query);
                if ($result == false) {
                    die(CLIUntils::colorizeConsoleOutput("\nMigrate error: $file\n\n", 'FAILURE'));
                }

                // TODO improve this code
                try {
                    if (method_exists((new $migration_class_name()), 'runAfterMigrationUp')) {
                        (new $migration_class_name())->runAfterMigrationUp();
                    }
                } catch (Exception $e) {
                    // rollback migration
                    $query = (new $migration_class_name())->down();
                    $result = mysqli_query(MyDB::db(), $query);
                    if ($result == false) {
                        die(CLIUntils::colorizeConsoleOutput("\nCan't rollback migration after run code after migration fail: $file\n\n", 'FAILURE'));
                    }

                    die(CLIUntils::colorizeConsoleOutput("\nRun code after migration up fail: $file\n\nERROR:" . $e->getMessage() . "\n\n", 'FAILURE'));
                }

                MigrationTools::incrementSchemaVersionIfSuccess($result, $version);
                $runs_migrations_arr[] = $file;
            }
        }

        return $runs_migrations_arr;
    }

    private function runRollback()
    {
        $db_setup = 'db_' . Config::get('env');
        MyDB::connect(Config::get($db_setup));

        // get last migration
        $version = MigrationTools::getLastMigrationVersion();

        // get file
        $migrations_path = MigrationTools::migrationsPath();
        $migration_filepath_arr = glob("$migrations_path/$version*.php");

        // jesli nie znaloeziono pliku z ta wesja to wywal blad
        if (empty($migration_filepath_arr)) {
            return ['message' => 'Not found migration rollback file.', 'status' => 'FAILURE'];
        } else {
            $migration_filepath = $migration_filepath_arr[0];
        }

        require_once $migration_filepath;
        $migration_class_name = MigrationTools::getClassNameFromFilename($migration_filepath);
        // get rollbck sql query
        $query = (new $migration_class_name())->down();
        $result = mysqli_query(MyDB::db(), $query);

        if ($result == false) {
            return ['message' => "Migrate rollback error: $migration_filepath", 'status' => 'FAILURE'];
        } else {
            MigrationTools::removeVersion($version);

            return ['message' => $migration_filepath, 'status' => 'SUCCESS'];
        }
    }

    private function dropAllTablesAndRecreate()
    {
        // Only in test env!
        Config::set('env', 'test');
        $db_setup = 'db_' . Config::get('env');
        MyDB::connect(Config::get($db_setup));
        MyDB::db()->query('SET foreign_key_checks = 0');
        if ($result = MyDB::db()->query('SHOW TABLES')) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                MyDB::db()->query('DROP TABLE IF EXISTS ' . $row[0]);
            }
        }
        MyDB::db()->query('SET foreign_key_checks = 1');
        MyDB::db()->close();
    }

    private function convertQueryToMysqlEngineMemory($query)
    {
        $query = str_replace('` text ', '` varchar(65535) ', $query); // 65535 max varchar allowed length in mysql engine memory
        $query = str_replace('` MEDIUMTEXT ', '` varchar(65535) ', $query);
        if (strpos($query, 'CREATE TABLE') !== false) {
            $query = $query . ' ENGINE = MEMORY';
        }

        return $query;
    }
}
