<?php

use App\Models\Accounting\Bill;
use App\Models\Accounting\Contact;
use App\Models\Accounting\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ChartOfAccountsSeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\FiscalPeriodSeeder']);
});

describe('Mark Overdue Invoices', function () {

    it('marks sent invoices as overdue when past due date', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->sent()
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        expect($invoice->status)->toBe(Invoice::STATUS_SENT);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_OVERDUE);
    });

    it('marks partial invoices as overdue when past due date', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->partial()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        expect($invoice->status)->toBe(Invoice::STATUS_PARTIAL);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_OVERDUE);
    });

    it('does not mark invoices that are not past due', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->sent()
            ->create([
                'due_date' => now()->addDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_SENT);
    });

    it('does not mark draft invoices as overdue', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->draft()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_DRAFT);
    });

    it('does not mark paid invoices as overdue', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->paid()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_PAID);
    });

    it('does not mark already overdue invoices again', function () {
        $customer = Contact::factory()->customer()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->overdue()
            ->create();

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $invoice->refresh();
        expect($invoice->status)->toBe(Invoice::STATUS_OVERDUE);
    });

});

describe('Mark Overdue Bills', function () {

    it('marks received bills as overdue when past due date', function () {
        $vendor = Contact::factory()->vendor()->create();

        $bill = Bill::factory()
            ->forContact($vendor)
            ->received()
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        expect($bill->status)->toBe(Bill::STATUS_RECEIVED);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $bill->refresh();
        expect($bill->status)->toBe(Bill::STATUS_OVERDUE);
    });

    it('marks partial bills as overdue when past due date', function () {
        $vendor = Contact::factory()->vendor()->create();

        $bill = Bill::factory()
            ->forContact($vendor)
            ->partial()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        expect($bill->status)->toBe(Bill::STATUS_PARTIAL);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $bill->refresh();
        expect($bill->status)->toBe(Bill::STATUS_OVERDUE);
    });

    it('does not mark bills that are not past due', function () {
        $vendor = Contact::factory()->vendor()->create();

        $bill = Bill::factory()
            ->forContact($vendor)
            ->received()
            ->create([
                'due_date' => now()->addDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $bill->refresh();
        expect($bill->status)->toBe(Bill::STATUS_RECEIVED);
    });

    it('does not mark draft bills as overdue', function () {
        $vendor = Contact::factory()->vendor()->create();

        $bill = Bill::factory()
            ->forContact($vendor)
            ->draft()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $bill->refresh();
        expect($bill->status)->toBe(Bill::STATUS_DRAFT);
    });

    it('does not mark paid bills as overdue', function () {
        $vendor = Contact::factory()->vendor()->create();

        $bill = Bill::factory()
            ->forContact($vendor)
            ->paid()
            ->create([
                'due_date' => now()->subDays(10),
            ]);

        $this->artisan('documents:mark-overdue')
            ->assertSuccessful();

        $bill->refresh();
        expect($bill->status)->toBe(Bill::STATUS_PAID);
    });

});

describe('Dry Run Mode', function () {

    it('does not update documents in dry run mode', function () {
        $customer = Contact::factory()->customer()->create();
        $vendor = Contact::factory()->vendor()->create();

        $invoice = Invoice::factory()
            ->forContact($customer)
            ->sent()
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        $bill = Bill::factory()
            ->forContact($vendor)
            ->received()
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        $this->artisan('documents:mark-overdue', ['--dry-run' => true])
            ->assertSuccessful();

        $invoice->refresh();
        $bill->refresh();

        expect($invoice->status)->toBe(Invoice::STATUS_SENT);
        expect($bill->status)->toBe(Bill::STATUS_RECEIVED);
    });

});

describe('Command Output', function () {

    it('shows summary table after execution', function () {
        $customer = Contact::factory()->customer()->create();
        $vendor = Contact::factory()->vendor()->create();

        Invoice::factory()
            ->forContact($customer)
            ->sent()
            ->count(3)
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        Bill::factory()
            ->forContact($vendor)
            ->received()
            ->count(2)
            ->create([
                'due_date' => now()->subDays(5),
            ]);

        $this->artisan('documents:mark-overdue')
            ->expectsOutputToContain('Invoices')
            ->expectsOutputToContain('Bills')
            ->assertSuccessful();
    });

    it('shows dry run warning when in dry run mode', function () {
        $this->artisan('documents:mark-overdue', ['--dry-run' => true])
            ->expectsOutputToContain('DRY RUN MODE')
            ->assertSuccessful();
    });

});
