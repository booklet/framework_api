<?php
class MyDB
{
    private static $db;

    public static function connect($db_setup)
    {
        $mysqli = new mysqli($db_setup['host'], $db_setup['user'], $db_setup['password'], $db_setup['name']);
        if ($mysqli->connect_errno) {
            die('MYSQL Connect Error: ' . $mysqli->connect_errno);
        } else {
            self::$db = $mysqli;
        }

        // Change character set to utf8
        if (!$mysqli->set_charset('utf8')) {
            die('Error loading character set utf8: ' . $mysqli->error);
        }
    }

    public static function db()
    {
        return self::$db;
    }

    public static function tablesList()
    {
        $query = MyDB::db()->prepare('SHOW TABLES');
        $query->execute();
        $result = $query->get_result();

        $tables = [];
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $value) {
                $tables[] = $value;
            }
        }

        $query->free_result();

        return $tables;
    }

    public static function clearTable($table_name)
    {
        $query = MyDB::db()->prepare('TRUNCATE TABLE ' . $table_name);

        if ($query->execute()) {
            return true;
        }
        return false;
    }

    public static function clearDatabaseExceptSchema()
    {
        $tables = MyDB::tablesList();

        // remove schema_migrations from table list
        $tables = array_diff($tables, array('schema_migrations'));

        foreach ($tables as $table_name) {
            if (!MyDB::clearTable($table_name)) {
                throw new Exception('Can\'t clear table: ' . $table_name);
            }
        }
    }
}
