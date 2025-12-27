<?php

namespace Database\Seeders\Demo;

use Database\Seeders\Demo\Vahana\VahanaContactSeeder;
use Database\Seeders\Demo\Vahana\VahanaProductSeeder;
use Database\Seeders\Demo\Vahana\VahanaTransactionSeeder;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data for the application.
     *
     * This seeder creates contextual demo data for PT Vahana (Electrical Panel Maker).
     * It includes:
     * - Master data (warehouses, product categories, users)
     * - Contacts (customers, vendors, subcontractors)
     * - Products with BOMs (raw materials, finished goods, services)
     * - Full transaction cycle (quotations, invoices, POs, work orders)
     *
     * Usage:
     *   php artisan db:seed --class=Database\\Seeders\\Demo\\DemoSeeder
     *
     * Or include in DatabaseSeeder with:
     *   php artisan db:seed --class=DatabaseSeeder
     *   (when demo mode is enabled)
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->command->info('║           PT VAHANA - ELECTRICAL PANEL MAKER                   ║');
        $this->command->info('║                    Demo Data Seeder                            ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // Seed master data first
        $this->command->info('📦 Seeding Master Data...');
        $this->call(MasterDataSeeder::class);
        $this->command->info('');

        // Seed Vahana-specific data
        $this->command->info('🏢 Seeding PT Vahana Context...');
        $this->command->info('');

        $this->command->info('  → Contacts (Customers, Vendors, Subcontractors)');
        $this->call(VahanaContactSeeder::class);

        $this->command->info('  → Products & BOMs');
        $this->call(VahanaProductSeeder::class);

        $this->command->info('  → Transactions (Quotations, Invoices, POs, Work Orders)');
        $this->call(VahanaTransactionSeeder::class);

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->command->info('║                    Demo Data Complete!                         ║');
        $this->command->info('╠═══════════════════════════════════════════════════════════════╣');
        $this->command->info('║  Demo Users:                                                   ║');
        $this->command->info('║    admin@demo.com     (password: password)                     ║');
        $this->command->info('║    sales@demo.com     (password: password)                     ║');
        $this->command->info('║    purchasing@demo.com (password: password)                    ║');
        $this->command->info('║    produksi@demo.com  (password: password)                     ║');
        $this->command->info('║    finance@demo.com   (password: password)                     ║');
        $this->command->info('║    gudang@demo.com    (password: password)                     ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
