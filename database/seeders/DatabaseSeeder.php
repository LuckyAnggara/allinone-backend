<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ItemBrand;
use App\Models\ItemBuyingPrice;
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
        \App\Models\Item::factory(6)->create();
        \App\Models\Branch::factory(5)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

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

        ItemBuyingPrice::created([
            'item_id' => '1',
            'price' => '24000',
        ]);

        ItemBuyingPrice::created([
            'item_id' => '2',
            'price' => '30000',
        ]);

        ItemBuyingPrice::created([
            'item_id' => '3',
            'price' => '60000',
        ]);

        ItemBuyingPrice::created([
            'item_id' => '4',
            'price' => '50000',
        ]);

        ItemBuyingPrice::created([
            'item_id' => '5',
            'price' => '100000',
        ]);
    }
}
