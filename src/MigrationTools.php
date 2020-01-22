<?php
class MigrationTools
{
    // migration files with pattern 20dddddddddd_*.php
    const MIGRATION_FILE_PATTERN = '20[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]_*.php';

    /**
     * @param string db/migrate/201607061958_CreateUsersTable.php
     *
     * @return string 201607061958
     */
    public static function getVersionFromFilename($file_name)
    {
        preg_match("/\d{12}/", $file_name, $output_array);

        return $output_array[0];
    }

    /**
     * @return string 201607061958
     */
    public static function getLastMigrationVersion()
    {
        $query = 'SELECT `schema_migrations`.`version` FROM `schema_migrations` ORDER BY `version` DESC LIMIT 1';
        $result = mysqli_query(MyDB::db(), $query);
        $version = mysqli_fetch_assoc($result)['version'];

        if (strlen($version) == 12) {
            return $version;
        } else {
            die('Can\'t get last migration version.');
        }
    }

    /**
     * @param string db/migrate/201607061958_CreateUsersTable.php
     *
     * @return string CreateUsersTable
     */
    public static function getClassNameFromFilename($file_name)
    {
        $file_name = pathinfo($file_name)['filename'];

        return explode('_', $file_name)[1];
    }

    /**
     * Before first migration create schema_migrations table if not exist.
     */
    public static function createSchemaMigrationsTable()
    {
        $query = 'SELECT version FROM schema_migrations';
        $result = mysqli_query(MyDB::db(), $query);

        if ($result == null) {
            $query = 'CREATE TABLE `schema_migrations` (
                `version` varchar(191) NOT NULL,
                UNIQUE KEY `unique_schema_migrations` (`version`)
            )';
            $result = mysqli_query(MyDB::db(), $query);
        }
    }

    /**
     * Check if that migration has been migrated.
     */
    public static function isMigratedMigration($version)
    {
        $query = "SELECT * FROM schema_migrations WHERE version='" . $version . "'";
        $result = mysqli_query(MyDB::db(), $query);

        return $result->num_rows == 1 ? true : false;
    }

    /**
     * Insert new migration version to schema_migrations table.
     */
    public static function incrementSchemaVersionIfSuccess($result, $version)
    {
        if (!empty($result)) {
            $query = "INSERT INTO schema_migrations (version) VALUES ($version)";
            mysqli_query(MyDB::db(), $query);
        }
    }

    /**
     * Check if all migrations are made.
     */
    public static function isAllMigrationsMade()
    {
        // Get last migration version
        $migrations_path = self::migrationsPath();

        $migrations_paths = glob($migrations_path . '/' . self::MIGRATION_FILE_PATTERN);
        $last_migration_path = array_pop($migrations_paths);
        $last_migration_to_migrate_version = self::getVersionFromFilename($last_migration_path);

        // Get last migration version from database
        $query = 'SELECT version FROM schema_migrations ORDER BY version DESC LIMIT 1';
        $result = mysqli_query(MyDB::db(), $query);
        $last_database_migration_version = mysqli_fetch_assoc($result)['version'];

        if ($last_migration_to_migrate_version == $last_database_migration_version) {
            return true;
        }

        return false;
    }

    /**
     * Remove passed version. Uses in rollback.
     */
    public static function removeVersion($version)
    {
        $query = "DELETE FROM schema_migrations WHERE version='" . $version . "'";
        $result = mysqli_query(MyDB::db(), $query);
    }

    /**
     * Get migration files path from config or get default.
     */
    public static function migrationsPath()
    {
        return Config::get('migrations_path') ?? 'db/migrate';
    }
}
