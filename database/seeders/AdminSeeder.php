<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
  public function run(): void
  {
    User::updateOrCreate(
      ['email' => 'admin@example.com'],
      [
        'name' => 'Админ',
        'password' => Hash::make('SuperSecret123'),
        'role' => 'admin', // если используешь роли
        'email_verified_at' => now(),
      ]
    );
  }
}
