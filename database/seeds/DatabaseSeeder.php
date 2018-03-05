<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Model::reguard();
        $this->call(ModulesTableSeeder::class);
        $this->call(FieldTypesTableSeeder::class);
        $this->call(ModelMetaTypesTableSeeder::class);
        $this->call(FieldsTableSeeder::class);
        $this->call(ModelMetaDataTableSeeder::class);
        $this->call(ModuleTypesTableSeeder::class);
        $this->call(MenuLinkTypesTableSeeder::class);
    }
}
