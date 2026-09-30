<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUsrSedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'superadmin@claimohub.test',
            ],
            [
                'name' => 'ClaimoHub superadmin',
                'password' => Hash::make('superAdmin123'),
                'role_user' => 'admin',
            ]
        );
    }
}
