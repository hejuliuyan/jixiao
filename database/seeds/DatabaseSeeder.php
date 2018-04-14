<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $seeders = [
        'PermissionSeeder',
        'RoleSeeder',
        'UserSeeder'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->seeders as $seedClass) {
            $this->call($seedClass);
        }
    }
}
