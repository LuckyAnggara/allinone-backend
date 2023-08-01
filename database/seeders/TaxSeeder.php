<?php

namespace Database\Seeders;

use App\Models\TaxDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        TaxDetail::create([
            'name' => 'Tanpa Pajak',
            'value' => 0,
        ]);


        TaxDetail::create([
            'name' => 'Pajak PPN 2023',
            'value' => 0.11,
        ]);

        TaxDetail::create([
            'name' => 'Pajak PPN 2022',
            'value' => 0.10,
        ]);
    }
}
