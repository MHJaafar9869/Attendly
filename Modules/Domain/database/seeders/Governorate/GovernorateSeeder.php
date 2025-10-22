<?php

namespace Modules\Domain\database\seeders\Governorate;

use Illuminate\Database\Seeder;
use Modules\Domain\Models\Governorate;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            ['name' => 'Ad Daqahlīyah', 'iso_code' => 'DK'],
            ['name' => 'Al Bahr al Ahmar', 'iso_code' => 'BA'],
            ['name' => 'Al Buhayrah', 'iso_code' => 'BH'],
            ['name' => 'Al Fayyūm', 'iso_code' => 'FYM'],
            ['name' => 'Al Gharbīyah', 'iso_code' => 'GH'],
            ['name' => 'Al Iskandarīyah', 'iso_code' => 'ALX'],
            ['name' => 'Al Ismāʿīlīyah', 'iso_code' => 'IS'],
            ['name' => 'Al Jīzah', 'iso_code' => 'GZ'],
            ['name' => 'Al Minūfīyah', 'iso_code' => 'MNF'],
            ['name' => 'Al Minyā', 'iso_code' => 'MN'],
            ['name' => 'Al Qāhirah', 'iso_code' => 'C'],
            ['name' => 'Al Qalyūbīyah', 'iso_code' => 'KB'],
            ['name' => 'Al Uqsur', 'iso_code' => 'LX'],
            ['name' => 'Al Wādī al Jadīd', 'iso_code' => 'WAD'],
            ['name' => 'Ash Sharqīyah', 'iso_code' => 'SHR'],
            ['name' => 'As Suways', 'iso_code' => 'SUZ'],
            ['name' => 'Aswān', 'iso_code' => 'ASN'],
            ['name' => 'Asyūt', 'iso_code' => 'AST'],
            ['name' => 'Banī Suwayf', 'iso_code' => 'BNS'],
            ['name' => 'Būr Saʿīd', 'iso_code' => 'PTS'],
            ['name' => 'Dumyāt', 'iso_code' => 'DT'],
            ['name' => 'Janūb Sīnāʾ', 'iso_code' => 'JS'],
            ['name' => 'Kafr ash Shaykh', 'iso_code' => 'KFS'],
            ['name' => 'Matrūh', 'iso_code' => 'MT'],
            ['name' => 'Qinā', 'iso_code' => 'KN'],
            ['name' => 'Shamal Sīnāʾ', 'iso_code' => 'SIN'],
            ['name' => 'Sūhāj', 'iso_code' => 'SHG'],
        ];

        foreach ($governorates as $gov) {
            Governorate::firstOrCreate(
                ['iso_code' => $gov['iso_code']],
                ['name' => $gov['name']]
            );
        }
    }
}
