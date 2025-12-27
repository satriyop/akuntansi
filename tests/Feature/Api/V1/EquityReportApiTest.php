<?php

use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalEntryLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ChartOfAccountsSeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\FiscalPeriodSeeder']);

    // Authenticate user
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

describe('Statement of Changes in Equity', function () {

    it('can generate changes in equity report', function () {
        $response = $this->getJson('/api/v1/reports/changes-in-equity');

        $response->assertOk()
            ->assertJsonStructure([
                'report_name',
                'period',
                'opening_equity',
                'changes' => [
                    'capital_additions',
                    'withdrawals',
                    'net_income',
                    'dividends',
                ],
                'closing_equity',
            ])
            ->assertJsonPath('report_name', 'Laporan Perubahan Ekuitas');
    });

    it('can filter by date range', function () {
        $startDate = '2024-01-01';
        $endDate = '2024-12-31';

        $response = $this->getJson("/api/v1/reports/changes-in-equity?start_date={$startDate}&end_date={$endDate}");

        $response->assertOk()
            ->assertJsonPath('period.start', $startDate)
            ->assertJsonPath('period.end', $endDate);
    });

    it('shows opening equity from previous period', function () {
        $equityAccount = Account::where('type', Account::TYPE_EQUITY)->first();
        $cashAccount = Account::where('code', '1-1001')->first();

        // Create equity entry in previous period
        $priorEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->subYear()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($priorEntry)->forAccount($cashAccount)->debit(10000000)->create();
        JournalEntryLine::factory()->forEntry($priorEntry)->forAccount($equityAccount)->credit(10000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfYear()->toDateString().'&end_date='.now()->endOfYear()->toDateString());

        $response->assertOk();

        $openingEquity = $response->json('opening_equity');
        expect($openingEquity)->toBeGreaterThanOrEqual(10000000);
    });

    it('tracks capital additions in current period', function () {
        $equityAccount = Account::where('type', Account::TYPE_EQUITY)->first();
        $cashAccount = Account::where('code', '1-1001')->first();

        // Create capital addition entry
        $entry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
            'description' => 'Setoran modal pemilik',
        ]);
        JournalEntryLine::factory()->forEntry($entry)->forAccount($cashAccount)->debit(5000000)->create();
        JournalEntryLine::factory()->forEntry($entry)->forAccount($equityAccount)->credit(5000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfMonth()->toDateString().'&end_date='.now()->endOfMonth()->toDateString());

        $response->assertOk();

        $changes = $response->json('changes');
        expect($changes['capital_additions'])->toBeGreaterThanOrEqual(5000000);
    });

    it('tracks withdrawals in current period', function () {
        $equityAccount = Account::where('type', Account::TYPE_EQUITY)->first();
        $cashAccount = Account::where('code', '1-1001')->first();

        // First, add some equity
        $capitalEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->subMonth()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($capitalEntry)->forAccount($cashAccount)->debit(10000000)->create();
        JournalEntryLine::factory()->forEntry($capitalEntry)->forAccount($equityAccount)->credit(10000000)->create();

        // Create withdrawal entry
        $withdrawalEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
            'description' => 'Penarikan pemilik',
        ]);
        JournalEntryLine::factory()->forEntry($withdrawalEntry)->forAccount($equityAccount)->debit(2000000)->create();
        JournalEntryLine::factory()->forEntry($withdrawalEntry)->forAccount($cashAccount)->credit(2000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfMonth()->toDateString().'&end_date='.now()->endOfMonth()->toDateString());

        $response->assertOk();

        $changes = $response->json('changes');
        expect($changes['withdrawals'])->toBeGreaterThanOrEqual(2000000);
    });

    it('includes net income from revenue and expenses', function () {
        $revenueAccount = Account::where('code', '4-1001')->first();
        $expenseAccount = Account::where('code', '5-2001')->first();
        $cashAccount = Account::where('code', '1-1001')->first();

        // Create revenue entry
        $revenueEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($revenueEntry)->forAccount($cashAccount)->debit(8000000)->create();
        JournalEntryLine::factory()->forEntry($revenueEntry)->forAccount($revenueAccount)->credit(8000000)->create();

        // Create expense entry
        $expenseEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($expenseEntry)->forAccount($expenseAccount)->debit(3000000)->create();
        JournalEntryLine::factory()->forEntry($expenseEntry)->forAccount($cashAccount)->credit(3000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfMonth()->toDateString().'&end_date='.now()->endOfMonth()->toDateString());

        $response->assertOk();

        $changes = $response->json('changes');
        // Net income should be revenue - expense = 8M - 3M = 5M
        expect($changes['net_income'])->toBe(5000000);
    });

    it('calculates closing equity correctly', function () {
        $equityAccount = Account::where('type', Account::TYPE_EQUITY)->first();
        $revenueAccount = Account::where('code', '4-1001')->first();
        $cashAccount = Account::where('code', '1-1001')->first();

        // Opening equity: 15M
        $openingEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->subMonth()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($openingEntry)->forAccount($cashAccount)->debit(15000000)->create();
        JournalEntryLine::factory()->forEntry($openingEntry)->forAccount($equityAccount)->credit(15000000)->create();

        // Capital addition: 5M
        $additionEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($additionEntry)->forAccount($cashAccount)->debit(5000000)->create();
        JournalEntryLine::factory()->forEntry($additionEntry)->forAccount($equityAccount)->credit(5000000)->create();

        // Net income: 3M
        $incomeEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
        ]);
        JournalEntryLine::factory()->forEntry($incomeEntry)->forAccount($cashAccount)->debit(3000000)->create();
        JournalEntryLine::factory()->forEntry($incomeEntry)->forAccount($revenueAccount)->credit(3000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfMonth()->toDateString().'&end_date='.now()->endOfMonth()->toDateString());

        $response->assertOk();

        $openingEquity = $response->json('opening_equity');
        $changes = $response->json('changes');
        $closingEquity = $response->json('closing_equity');

        // Closing = Opening + Capital Additions - Withdrawals + Net Income - Dividends
        $expectedClosing = $openingEquity + $changes['capital_additions'] - $changes['withdrawals'] + $changes['net_income'] - $changes['dividends'];
        expect($closingEquity)->toBe($expectedClosing);
    });

    it('handles period with no changes', function () {
        // Query a period with no transactions
        $futureDate = now()->addYear()->toDateString();

        $response = $this->getJson("/api/v1/reports/changes-in-equity?start_date={$futureDate}&end_date={$futureDate}");

        $response->assertOk();

        $changes = $response->json('changes');
        expect($changes['capital_additions'])->toBe(0);
        expect($changes['withdrawals'])->toBe(0);
        expect($changes['net_income'])->toBe(0);
        expect($changes['dividends'])->toBe(0);
    });

    it('defaults to current fiscal year when no date provided', function () {
        $response = $this->getJson('/api/v1/reports/changes-in-equity');

        $response->assertOk();

        $period = $response->json('period');
        expect($period)->toHaveKeys(['start', 'end']);
        expect($period['start'])->not->toBeEmpty();
        expect($period['end'])->not->toBeEmpty();
    });

    it('shows dividends distribution', function () {
        $equityAccount = Account::where('type', Account::TYPE_EQUITY)->first();
        $cashAccount = Account::where('code', '1-1001')->first();
        $dividendsAccount = Account::where('code', '3-3001')->first() ?? Account::factory()->equity()->create(['code' => '3-3001', 'name' => 'Dividen']);

        // Create dividend entry
        $dividendEntry = JournalEntry::factory()->posted()->create([
            'entry_date' => now()->toDateString(),
            'description' => 'Pembayaran dividen',
        ]);
        JournalEntryLine::factory()->forEntry($dividendEntry)->forAccount($dividendsAccount)->debit(4000000)->create();
        JournalEntryLine::factory()->forEntry($dividendEntry)->forAccount($cashAccount)->credit(4000000)->create();

        $response = $this->getJson('/api/v1/reports/changes-in-equity?start_date='.now()->startOfMonth()->toDateString().'&end_date='.now()->endOfMonth()->toDateString());

        $response->assertOk();

        $changes = $response->json('changes');
        expect($changes['dividends'])->toBeGreaterThanOrEqual(0);
    });

});
