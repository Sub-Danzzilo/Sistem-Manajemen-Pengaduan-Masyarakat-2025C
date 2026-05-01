<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Akun debug untuk kebutuhan pengembangan (Autofill Login).
     * Cukup edit array ini untuk menambah/mengurangi user debug.
     */
    public static array $debugAccounts = [
        [
            'name' => 'Admin 1',
            'email' => 'user1@admin.go.id',
            'password' => 'atadmindotgodotaidi1',
            'role' => \App\Models\User::ROLE_ADMIN,
            'color' => 'bg-red-50 text-red-700 border-red-100',
        ],
        [
            'name' => 'Unit Instansi 1',
            'email' => 'unit1@instansi.go.id',
            'password' => 'atinstansidotgodotaidi1',
            'role' => \App\Models\User::ROLE_INSTANSI,
            'color' => 'bg-blue-50 text-blue-700 border-blue-100',
        ],
        [
            'name' => 'Rian Rain',
            'email' => 'Rianrain@fakemail.com',
            'password' => 'rianismyname',
            'role' => \App\Models\User::ROLE_MASYARAKAT,
            'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        ],
        [
            'name' => 'Clararissa Margaret',
            'email' => 'Clararissa@fakemail.com',
            'password' => 'claramybaby90',
            'role' => \App\Models\User::ROLE_MASYARAKAT,
            'color' => 'bg-purple-50 text-purple-700 border-purple-100',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::$debugAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
