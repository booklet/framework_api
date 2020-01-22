<?php
class CreateTesterParentModels
{
    public function up()
    {
        $query = 'CREATE TABLE `tester_parent_models` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(191) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        )';

        return $query;
    }

    public function down()
    {
        $query = 'DROP TABLE `tester_parent_models`';

        return $query;
    }
}
