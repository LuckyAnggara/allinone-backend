<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Bank::create([
            'name' => 'BNI',
            'number_account' => "0468995561",
            'name_account'=> "Lucky Anggara",
            'created_by'=> 1,
            'branch_id'=> 1
        ]);

        Bank::create([
            'name' => 'BCA',
            'number_account' => "12345678",
            'name_account'=> "Lucky Anggara",
            'created_by'=> 1,
            'branch_id'=> 1
        ]);

        Bank::create([
            'name' => 'BRI',
            'number_account' => "545646546546",
            'name_account'=> "Lucky Anggara",
            'created_by'=> 1,
            'branch_id'=> 1
        ]);
    }
}
