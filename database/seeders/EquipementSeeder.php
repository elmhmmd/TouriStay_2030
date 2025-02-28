<?php

namespace Database\Seeders;

use App\Models\Equipement;
use Illuminate\Database\Seeder;

class EquipementSeeder extends Seeder
{
    public function run(): void
    {
        Equipement::create(['name' => 'WiFi']);
        Equipement::create(['name' => 'Pool']);
        Equipement::create(['name' => 'Parking']);
        Equipement::create(['name' => 'Air Conditioning']);
    }
}
