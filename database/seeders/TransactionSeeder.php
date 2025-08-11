<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $transactions = [
            [
                'user_id' => $users->random()->id,
                'point' => 1000.00,
                'point_change' => 1000.00,
                'type_transaction' => 'recharge_point',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 500.00,
                'point_change' => 500.00,
                'type_transaction' => 'recharge_point',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 800.00,
                'point_change' => 800.00,
                'type_transaction' => 'recharge_point',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 750.00,
                'point_change' => -50.00,
                'type_transaction' => 'bid',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 600.00,
                'point_change' => -200.00,
                'type_transaction' => 'buy_product',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 1200.00,
                'point_change' => 400.00,
                'type_transaction' => 'recharge_point',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 950.00,
                'point_change' => -250.00,
                'type_transaction' => 'bid',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 300.00,
                'point_change' => -300.00,
                'type_transaction' => 'buy_product',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 1500.00,
                'point_change' => 300.00,
                'type_transaction' => 'recharge_point',
            ],
            [
                'user_id' => $users->random()->id,
                'point' => 200.00,
                'point_change' => -100.00,
                'type_transaction' => 'bid',
            ],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }

        $this->command->info('TransactionSeeder completed successfully!');
    }
}
