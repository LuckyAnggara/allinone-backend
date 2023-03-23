<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::created([
            'name' => 'Company A',
            'branch_code' => 'BR001',
            'address' => '123 Main St',
            'phone_number' => '555-1234',
            'head_id' => 1,
            'email' => 'companya@example.com',
            'fax_number' => '555-4321',
        ]);

        Branch::created([
            'name' => 'Company A',
            'branch_code' => 'BR001',
            'address' => '123 Main St',
            'phone_number' => '555-1234',
            'head_id' => 1,
            'email' => 'companya@example.com',
            'fax_number' => '555-4321',
        ]);
    }
}
