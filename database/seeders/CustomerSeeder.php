<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'name' => 'John Doe',
            'address' => '123 Main St',
            'phone_number' => '555-1234',
            'member' => true,
            'company' => false,
            'pic' => null,
            'created_by' => 1,
            'branch_id' => 1
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'address' => '456 Elm St',
            'phone_number' => '555-5678',
            'member' => false,
            'company' => true,
            'pic' => null,
            'created_by' => 1,
            'branch_id' => 1
        ]);

        Customer::create([
            'name' => 'Bob Johnson',
            'address' => '789 Oak St',
            'phone_number' => '555-9012',
            'member' => true,
            'company' => false,
            'pic' => null,
            'created_by' => 1,
            'branch_id' => 1
        ]);

        Customer::create([
            'name' => 'Sara Lee',
            'address' => '101 Cherry Lane',
            'phone_number' => '555-3456',
            'member' => false,
            'company' => true,
            'pic' => null,
            'created_by' => 1,
            'branch_id' => 1
        ]);

        Customer::create([
            'name' => 'Mike Brown',
            'address' => '246 Pine St',
            'phone_number' => '555-7890',
            'member' => true,
            'company' => false,
            'pic' => null,
            'created_by' => 1,
            'branch_id' => 1
        ]);
    }
}
