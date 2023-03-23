<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::created([
            'name' => 'SUPER USER',
            'ability' => '[{"action" : "manage", "subject" : "all"}]',
            'header' => 'superuser',
        ]);

        Role::created([
            'name' => 'KEPALA CABANG',
            'ability' =>
                '[{"action" : "manage", "subject" : "header"},{"action" : "manage", "subject" : "auth"},{"action" : "manage", "subject" : "dashboard_cabang"},{"action" : "manage", "subject" : "laporan_persediaan"},{"action" : "manage", "subject" : "laporan_kasir"},{"action" : "manage", "subject" : "laporan_cabang"},{"action" : "manage", "subject" : "laporan_neraca"},{"action" : "manage", "subject" : "laporan_laba_rugi"},{"action" : "manage", "subject" : "master_barang"},{"action" : "manage", "subject" : "master_kontak"},{"action" : "manage", "subject" : "transaksi_penjualan"},{"action" : "manage", "subject" : "transaksi_po"},{"action" : "manage", "subject" : "keuangan_beban"},{"action" : "manage", "subject" : "keuangan_akuntansi"},{"action" : "manage", "subject" : "keuangan_akuntansi_neraca"},{"action" : "manage", "subject" : "keuangan_akuntansi_laba_rugi"},{"action" : "manage", "subject" : "keuangan_akuntansi_daftar"},{"action" : "manage", "subject" : "keuangan_kas"},{"action" : "manage", "subject" : "keuangan_utang_piutang"}]',
            'header' => 'cabang',
        ]);

        Role::created([
            'name' => 'MANAJER',
            'ability' => '[{"action" : "manage", "subject" : "all"}]',
            'header' => 'manajer',
        ]);

        Role::created([
            'name' => 'KASIR',
            'ability' => '[{"action" : "manage", "subject" : "header"},{"action" : "manage", "subject" : "auth"},{"action" : "manage", "subject" : "dashboard_kasir"},{"action" : "manage", "subject" : "laporan_kasir"},{"action" : "manage", "subject" : "master_kontak"},{"action" : "manage", "subject" : "transaksi_penjualan"},{"action" : "manage", "subject" : "keuangan_kas"},{"action" : "manage", "subject" : "keuangan_akuntansi"}]',
            'header' => 'kasir',
        ]);
    }
}
