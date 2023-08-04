<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Account;
use App\Models\Customer;
use App\Models\ItemBeginningStock;
use App\Models\ItemBrand;
use App\Models\ItemUnit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        \App\Models\Employee::factory(5)->create();
        // \App\Models\User::factory(2)->create();
        \App\Models\Item::factory(100)->create();
        \App\Models\Branch::factory(5)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            TaxSeeder::class,
            BankSeeder::class,
            CategoriesSeeder::class,
            UserSeeder::class,

        ]);


        ItemUnit::create([
            'name' => 'Kilogram'
        ]);
        ItemUnit::create([
            'name' => 'Meter'
        ]);
        ItemUnit::create([
            'name' => 'Centimeter'
        ]);
        ItemUnit::create([
            'name' => 'Gram'
        ]);
        ItemUnit::create([
            'name' => 'Ton'
        ]);
        ItemUnit::create([
            'name' => 'Buah'
        ]);
        ItemUnit::create([
            'name' => 'Lembar'
        ]);
        ItemUnit::create([
            'name' => 'Roll'
        ]);
        ItemUnit::create([
            'name' => 'Sack'
        ]);


        ItemBrand::create([
            'name' => 'BBM Trust'
        ]);
        ItemBrand::create([
            'name' => 'BBM'
        ]);
        Warehouse::create([
            'name' => 'Pusat',
            'address' => 'Jl Raya Limbangan 1',
            'pic_id' => 6
        ]);
        Warehouse::create([
            'name' => 'Pabrik',
            'address' => 'Jl Raya Limbangan 2',
            'pic_id' => 7
        ]);

        ItemBeginningStock::create([
            'item_id' => '1',
            'stock' => 20,
            'price' => '24000',
            'notes' => 'periode_awal_03072023'
        ]);

        ItemBeginningStock::create([
            'item_id' => '2',
            'stock' => 70,
            'price' => '30000',
            'notes' => 'periode_awal_03072023'
        ]);

        ItemBeginningStock::create([
            'item_id' => '3',
            'stock' => 60,
            'price' => '60000',
            'notes' => 'periode_awal_03072023'
        ]);

        ItemBeginningStock::create([
            'item_id' => '4',
            'stock' => 200,
            'price' => '50000',
            'notes' => 'periode_awal_03072023'
        ]);

        ItemBeginningStock::create([
            'item_id' => '5',
            'stock' => 100,
            'price' => '100000',
            'notes' => 'periode_awal_03072023'
        ]);

        Account::create([
            'account_no' => '1',
            'name' => 'PENJUALAN',
            'category' => 'LABA_RUGI',
            'type' => 'KREDIT',
        ]);

        Account::create([
            'account_no' => '2',
            'name' => 'RETUR PENJUALAN',
            'category' => 'LABA_RUGI',
            'type' => 'DEBIT',
        ]);

        Account::create([
            'account_no' => '3',
            'name' => 'DISKON',
            'category' => 'LABA_RUGI',
            'type' => 'DEBIT',
        ]);

        Account::create([
            'account_no' => '4',
            'name' => 'PENJUALAN KOTOR',
            'category' => 'LABA_RUGI',
            'type' => 'KREDIT',
        ]);

        Account::create([
            'account_no' => '5',
            'name' => 'PERSEDIAAN AWAL',
            'category' => 'LABA_RUGI',
            'type' => 'DEBIT',
        ]);

        Account::create([
            'account_no' => '6',
            'name' => 'PEMBELIAN',
            'category' => 'LABA_RUGI',
            'type' => 'KREDIT',
        ]);

        Account::create([
            'account_no' => '7',
            'name' => 'PERSEDIAAN AKHIR',
            'category' => 'LABA_RUGI',
            'type' => 'DEBIT',
        ]);

        Account::create([
            'account_no' => '8',
            'name' => 'HARGA POKOK PENJUALAN',
            'category' => 'LABA_RUGI',
            'type' => 'DEBIT',
        ]);

        Account::create([
            'account_no' => '9',
            'name' => 'LABA KOTOR',
            'category' => 'LABA_RUGI',
            'type' => 'KREDIT',
        ]);

        Customer::create([
            'id' => '1',
            'name' => '-',
            'address' => '-',
            'phone_number' => '-',
            'member' => 1,
            'created_by' => 1,
            'branch_id' => 0,
        ]);
    }
}
