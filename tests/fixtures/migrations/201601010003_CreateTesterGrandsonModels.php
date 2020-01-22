<?php
class CreateTesterGrandsonModels
{
    public function up()
    {
        $query = 'CREATE TABLE `tester_grandson_models` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tester_child_model_id` int(11) NOT NULL,
            `description` varchar(191) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        )';

        return $query;
    }

    public function down()
    {
        $query = 'DROP TABLE `tester_grandson_models`';

        return $query;
    }
}
