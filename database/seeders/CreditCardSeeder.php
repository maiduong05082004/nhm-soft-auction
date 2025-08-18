<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Utils\HelperFunc;
use App\Models\User;

class CreditCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::all();
        $creditCards = [
            [
                'id' => HelperFunc::getTimestampAsId(),
                'name' => 'MAI DUC DUONG',
                'bank' => 'TPBank',
                'card_number' => '19805082004',
                'user_id' => $user->random()->first()->id,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($creditCards as $creditCard) {
            DB::table('credit_cards')->insert($creditCard);
        }
    }
}