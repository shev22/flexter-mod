<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@flexter.test',
        ]);

        User::factory(9)->create();
    }
}
