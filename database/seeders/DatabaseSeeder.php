<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Coordinator;
use App\Models\Resident;
use App\Models\Account;
use App\Models\Period;
use App\Models\Category;
use App\Models\ContributionType;
use App\Models\ContributionPeriod;
use App\Models\ContributionPayment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin RT',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        $user = User::create([
            'name' => 'Warga RT',
            'email' => 'warga@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'USER',
        ]);

        // 2. Seed Coordinators
        $coord1 = Coordinator::create([
            'name' => 'Budi Santoso',
            'managed_blocks' => 'A',
        ]);

        $coord2 = Coordinator::create([
            'name' => 'Siti Rahma',
            'managed_blocks' => 'B',
        ]);

        // 3. Seed Residents
        $residentsData = [
            ['name' => 'Joko Widodo', 'block' => 'A', 'house_number' => '12', 'phone' => '08111111111', 'coordinator_id' => $coord1->id],
            ['name' => 'Prabowo Subianto', 'block' => 'A', 'house_number' => '15', 'phone' => '08111111112', 'coordinator_id' => $coord1->id],
            ['name' => 'Ganjar Pranowo', 'block' => 'B', 'house_number' => '03', 'phone' => '08111111113', 'coordinator_id' => $coord2->id],
            ['name' => 'Anies Baswedan', 'block' => 'B', 'house_number' => '07', 'phone' => '08111111114', 'coordinator_id' => $coord2->id],
            ['name' => 'Ridwan Kamil', 'block' => 'C', 'house_number' => '01', 'phone' => '08111111115', 'coordinator_id' => null],
            ['name' => 'Khofifah Indar', 'block' => 'C', 'house_number' => '05', 'phone' => '08111111116', 'coordinator_id' => null],
        ];

        $residents = [];
        foreach ($residentsData as $r) {
            $residents[] = Resident::create($r);
        }

        // 4. Seed Accounts
        $account = Account::create([
            'name' => 'Kas Utama RT',
            'slug' => 'kas-utama-rt',
            'description' => 'Buku kerja utama pengelolaan uang kas RT 01.',
        ]);

        // 5. Seed Periods
        $periodJune = Period::create([
            'account_id' => $account->id,
            'month' => 6,
            'year' => 2026,
            'is_published' => true,
        ]);

        $periodJuly = Period::create([
            'account_id' => $account->id,
            'month' => 7,
            'year' => 2026,
            'is_published' => false, // Draft
        ]);

        // 6. Seed Categories
        $catSosial = Category::create([
            'account_id' => $account->id,
            'name' => 'Sosial & Santunan',
        ]);

        $catPembangunan = Category::create([
            'account_id' => $account->id,
            'name' => 'Pembangunan Fisik',
        ]);

        $catOperasional = Category::create([
            'account_id' => $account->id,
            'name' => 'Operasional RT',
        ]);

        // 7. Seed Contribution Type
        $iuranRutin = ContributionType::create([
            'name' => 'Iuran Rutin RT',
            'description' => 'Iuran wajib bulanan warga RT 01 untuk kas sosial dan kebersihan.',
            'is_public' => true,
        ]);

        // 8. Seed Contribution Period (June 2026)
        $contribPeriod = ContributionPeriod::create([
            'contribution_type_id' => $iuranRutin->id,
            'month' => 6,
            'year' => 2026,
            'amount_per_resident' => 50000,
        ]);

        // 9. Seed Contribution Payments (Lunas 4 orang, Belum 2 orang)
        $paymentStatuses = [
            ['resident_id' => $residents[0]->id, 'status' => 'PAID', 'amount' => 50000],
            ['resident_id' => $residents[1]->id, 'status' => 'PAID', 'amount' => 50000],
            ['resident_id' => $residents[2]->id, 'status' => 'PAID', 'amount' => 50000],
            ['resident_id' => $residents[3]->id, 'status' => 'PAID', 'amount' => 50000],
            ['resident_id' => $residents[4]->id, 'status' => 'UNPAID', 'amount' => 0],
            ['resident_id' => $residents[5]->id, 'status' => 'UNPAID', 'amount' => 0],
        ];

        foreach ($paymentStatuses as $ps) {
            ContributionPayment::create([
                'contribution_period_id' => $contribPeriod->id,
                'resident_id' => $ps['resident_id'],
                'amount_paid' => $ps['amount'],
                'payment_status' => $ps['status'],
                'paid_at' => $ps['status'] === 'PAID' ? now() : null,
            ]);
        }

        // 10. Seed Transactions for June 2026 Period
        // Link to the June 2026 Contribution
        Transaction::create([
            'period_id' => $periodJune->id,
            'category_id' => null,
            'contribution_period_id' => $contribPeriod->id,
            'type' => 'IN',
            'name' => 'Penerimaan Iuran Rutin RT - Juni 2026',
            'day' => 15,
            'quantity' => null,
            'amount' => 200000, // 4 orang * 50.000
            'description' => 'Sinkronisasi otomatis penerimaan iuran bulanan warga.',
        ]);

        // Normal Transaction
        Transaction::create([
            'period_id' => $periodJune->id,
            'category_id' => $catOperasional->id,
            'contribution_period_id' => null,
            'type' => 'OUT',
            'name' => 'Pembelian ATK dan Fotokopi',
            'day' => 10,
            'quantity' => 1,
            'amount' => 35000,
            'description' => 'Membeli perlengkapan administrasi RT.',
        ]);

        Transaction::create([
            'period_id' => $periodJune->id,
            'category_id' => $catSosial->id,
            'contribution_period_id' => null,
            'type' => 'OUT',
            'name' => 'Santunan Warga Sakit',
            'day' => 20,
            'quantity' => 1,
            'amount' => 100000,
            'description' => 'Santunan untuk keluarga Pak Joko.',
        ]);

        // 11. Seed App Settings
        \App\Models\Setting::create([
            'key' => 'ketua_rt',
            'value' => 'Budi Santoso',
        ]);

        \App\Models\Setting::create([
            'key' => 'bendahara',
            'value' => ['Siti Rahma', 'Ahmad Ridwan'],
        ]);

        \App\Models\Setting::create([
            'key' => 'kontak_admin',
            'value' => '+62 896-8458-6168',
        ]);
    }
}
