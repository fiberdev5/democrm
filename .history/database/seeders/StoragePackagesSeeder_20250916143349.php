<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoragePackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Ek 5GB Depolama',
                'description' => 'Küçük işletmeler için ideal. Yaklaşık 1.250 adet yüksek kaliteli fotoğraf veya 500 adet PDF belgesi saklayabilirsiniz.',
                'storage_gb' => 5.00,
                'price' => 49.99,
                'currency' => 'TRY',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Ek 15GB Depolama',
                'description' => 'Orta ölçekli işletmeler için popüler seçenek. Yaklaşık 3.750 adet fotoğraf, servis belgeleri ve fatura arşivi için yeterli alan.',
                'storage_gb' => 15.00,
                'price' => 119.99,
                'currency' => 'TRY',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Ek 50GB Depolama',
                'description' => 'Büyük işletmeler ve yoğun kullanım için. Video kayıtları, kapsamlı belge arşivi ve binlerce fotoğraf için geniş alan.',
                'storage_gb' => 50.00,
                'price' => 299.99,
                'currency' => 'TRY',
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($packages as $package) {
            StoragePackage::create($package);
        }
    }
}
