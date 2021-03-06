<?php


use Phinx\Seed\AbstractSeed;

class UsersGrantsMapSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return ['UserSeeder', 'GrantsSeeder'];
    }
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->execute('TRUNCATE TABLE tbl_fact_map_grant_users');

        $sql = file_get_contents(__DIR__ . '/../sql/018_add_user_grant_map.sql');
        $this->execute($sql);
    }
}
