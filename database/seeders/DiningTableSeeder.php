<?php

namespace Database\Seeders;

use App\Enums\TableSize;
use App\Enums\TableStatus;
use App\Models\DiningTable;
use Illuminate\Database\Seeder;

class DiningTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['code' => 'T-01', 'name' => 'Table 1', 'area' => 'Main Hall', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 126, 'status' => TableStatus::Available, 'sort_order' => 1],
            ['code' => 'T-02', 'name' => 'Table 2', 'area' => 'Main Hall', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 126, 'status' => TableStatus::Occupied, 'sort_order' => 2],
            ['code' => 'T-03', 'name' => 'Table 3', 'area' => 'Main Hall', 'capacity' => 8, 'size' => TableSize::Large, 'price' => 126, 'status' => TableStatus::Reserved, 'sort_order' => 3],
            ['code' => 'T-04', 'name' => 'Table 4', 'area' => 'Main Hall', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 126, 'status' => TableStatus::Available, 'sort_order' => 4],
            ['code' => 'T-05', 'name' => 'Table 5', 'area' => 'Main Hall', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 126, 'status' => TableStatus::Occupied, 'sort_order' => 5],
            ['code' => 'T-06', 'name' => 'Table 6', 'area' => 'Main Hall', 'capacity' => 8, 'size' => TableSize::Large, 'price' => 126, 'status' => TableStatus::Available, 'sort_order' => 6],
            ['code' => 'T-07', 'name' => 'Table 7', 'area' => 'Patio', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 98, 'status' => TableStatus::Occupied, 'sort_order' => 7],
            ['code' => 'T-08', 'name' => 'Table 8', 'area' => 'Patio', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 110, 'status' => TableStatus::Reserved, 'sort_order' => 8],
            ['code' => 'T-09', 'name' => 'Table 9', 'area' => 'Patio', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 98, 'status' => TableStatus::Available, 'sort_order' => 9],
            ['code' => 'T-10', 'name' => 'Table 10', 'area' => 'Patio', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 110, 'status' => TableStatus::Available, 'sort_order' => 10],
            ['code' => 'T-11', 'name' => 'Table 11', 'area' => 'VIP Lounge', 'capacity' => 8, 'size' => TableSize::Large, 'price' => 180, 'status' => TableStatus::Occupied, 'sort_order' => 11],
            ['code' => 'T-12', 'name' => 'Table 12', 'area' => 'VIP Lounge', 'capacity' => 8, 'size' => TableSize::Large, 'price' => 180, 'status' => TableStatus::Available, 'sort_order' => 12],
            ['code' => 'T-13', 'name' => 'Table 13', 'area' => 'VIP Lounge', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 150, 'status' => TableStatus::Available, 'sort_order' => 13],
            ['code' => 'T-14', 'name' => 'Table 14', 'area' => 'Bar Area', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 85, 'status' => TableStatus::Occupied, 'sort_order' => 14],
            ['code' => 'T-15', 'name' => 'Table 15', 'area' => 'Bar Area', 'capacity' => 4, 'size' => TableSize::Small, 'price' => 85, 'status' => TableStatus::Available, 'sort_order' => 15],
            ['code' => 'T-16', 'name' => 'Table 16', 'area' => 'Bar Area', 'capacity' => 6, 'size' => TableSize::Medium, 'price' => 95, 'status' => TableStatus::Available, 'sort_order' => 16],
        ];

        foreach ($tables as $table) {
            DiningTable::query()->updateOrCreate(
                ['code' => $table['code']],
                $table,
            );
        }
    }
}
