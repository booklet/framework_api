<?php
class CreateFWTestModelUser
{
    public function up()
    {
        $query = "CREATE TABLE `fw_test_model_users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(191) NOT NULL,
            `email` varchar(191) NOT NULL,
            `role` varchar(191) NOT NULL,
            `password_digest` varchar(191) NOT NULL,
            `created_at` datetime NOT NULL,
            `updated_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
        )";
        return $query;
    }

    public function down() {
        $query = "DROP TABLE `fw_test_model_users`";
        return $query;
    }
}
