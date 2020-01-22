<?php
class CreateTesterChildModels
{
    public function up()
    {
        $query = 'CREATE TABLE `tester_child_models` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tester_parent_model_id` int(11) NOT NULL,
            `address` varchar(191) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        )';

        return $query;
    }

    public function down()
    {
        $query = 'DROP TABLE `tester_child_models`';

        return $query;
    }
}
