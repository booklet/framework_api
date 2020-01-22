<?php
class MigrationToolsTest extends \CustomPHPUnitTestCase
{
    public function testGetVersionFromFilename()
    {
        $mt = new MigrationTools();
        $version = $mt->getVersionFromFilename('db/migrate/201607061958_CreateUsersTable.php');

        $this->assertEquals($version, '201607061958');
    }

    public function testGetLastMigrationVersion()
    {
        $mt = new MigrationTools();
        $version = $mt->getLastMigrationVersion();

        $this->assertEquals($version, '201601010003');
    }

    public function testGetClassNameFromFilename()
    {
        $mt = new MigrationTools();
        $class_name = $mt->getClassNameFromFilename('db/migrate/201607061958_CreateUsersTable.php');

        $this->assertEquals($class_name, 'CreateUsersTable');
    }

    public function testisMigratedMigration()
    {
        $mt = new MigrationTools();
        $is_migrated = $mt->isMigratedMigration('201601010000');

        $this->assertEquals($is_migrated, true);

        $is_migrated = $mt->isMigratedMigration('202001010000');

        $this->assertEquals($is_migrated, false);
    }
}
