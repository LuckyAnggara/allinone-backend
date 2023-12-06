<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'employee_id' => 1,
            'username' => 'demo',
            'password' => '$2y$10$SKX.kBjyiXwCnnEo2jMsD.aCIuKhS52PeLqsQc21N7Ix3MRZ8rB6O',
            'branch_id' => 1,
            'role_id' => 4,
        ]);
    }
}
