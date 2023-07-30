<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Account;
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
        \App\Models\User::factory(2)->create();
        \App\Models\Item::factory(8)->create();
        \App\Models\Branch::factory(5)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        ItemUnit::create([
            'item_id' => '1',
            'name' => 'Dus',
            'price' => '20000'
        ]);
        ItemUnit::create([
            'item_id' => '1',
            'name' => 'Pieces',
            'price' => '2400'
        ]);
        ItemUnit::create([
            'item_id' => '2',
            'name' => 'KG',
            'price' => '100000'
        ]);
        ItemUnit::create([
            'item_id' => '2',
            'name' => 'Roll',
            'price' => '25000'
        ]);
        ItemUnit::create([
            'item_id' => '3',
            'name' => 'Lusin',
            'price' => '100000'
        ]);
        ItemUnit::create([
            'item_id' => '3',
            'name' => 'Pieces',
            'price' => '10000'
        ]);
        ItemUnit::create([
            'item_id' => '4',
            'name' => 'Rim',
            'price' => '25000'
        ]);
        ItemUnit::create([
            'item_id' => '4',
            'name' => 'Lembar',
            'price' => '500'
        ]);
        ItemUnit::create([
            'item_id' => '5',
            'name' => 'Dus',
            'price' => '20000'
        ]);
        ItemUnit::create([
            'item_id' => '5',
            'name' => 'Pieces',
            'price' => '2400'
        ]);
        ItemUnit::create([
            'item_id' => '6',
            'name' => 'KG',
            'price' => '100000'
        ]);
        ItemUnit::create([
            'item_id' => '6',
            'name' => 'Roll',
            'price' => '25000'
        ]);
        ItemUnit::create([
            'item_id' => '7',
            'name' => 'Lusin',
            'price' => '100000'
        ]);
        ItemUnit::create([
            'item_id' => '7',
            'name' => 'Pieces',
            'price' => '10000'
        ]);
        ItemUnit::create([
            'item_id' => '8',
            'name' => 'Rim',
            'price' => '25000'
        ]);
        ItemUnit::create([
            'item_id' => '8',
            'name' => 'Lembar',
            'price' => '500'
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
    }
}
