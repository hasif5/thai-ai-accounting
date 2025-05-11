<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LedgerEntry;
use App\Services\AssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class AIInvoiceE2ETest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the COA seeder
        $this->seed(\Database\Seeders\ChartOfAccountsSeeder::class);

        // Create a demo customer
        $this->customer = Customer::create([
            'name' => 'บริษัท ทดสอบ จำกัด',
            'tax_id' => '0123456789012',
            'address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
            'phone' => '02-123-4567',
            'email' => 'contact@test.co.th'
        ]);

        // Mock the AI service
        $this->mockAssistantService = Mockery::mock(AssistantService::class);
        $this->app->instance(AssistantService::class, $this->mockAssistantService);

        // Create storage directory for XML files
        Storage::makeDirectory('invoices/xml');
    }

    public function test_complete_invoice_generation_flow(): void
    {
        // Prepare mock AI response
        $mockResponse = json_encode([
            'invoice_number' => 'INV-' . date('Ymd') . '-001',
            'customer_id' => $this->customer->id,
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'subtotal' => 10000.00,
            'tax' => 700.00,
            'total' => 10700.00,
            'lines' => [
                [
                    'description' => 'บริการพัฒนาซอฟต์แวร์',
                    'quantity' => 1,
                    'unit_price' => 10000.00,
                    'tax_rate' => 7.00,
                    'tax_amount' => 700.00,
                    'subtotal' => 10000.00,
                    'total' => 10700.00
                ]
            ]
        ]);

        // Mock the AI service response
        $this->mockAssistantService
            ->shouldReceive('getResponse')
            ->with('invoice_create', ['description' => 'ค่าพัฒนาซอฟต์แวร์ระบบบัญชี'])
            ->once()
            ->andReturn($mockResponse);

        // Execute the command
        $this->artisan('make:ai-invoice', [
            'description' => 'ค่าพัฒนาซอฟต์แวร์ระบบบัญชี'
        ])->assertSuccessful();

        // Assert invoice was created
        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertEquals('INV-' . date('Ymd') . '-001', $invoice->invoice_number);
        $this->assertEquals($this->customer->id, $invoice->customer_id);
        $this->assertEquals(10700.00, $invoice->total);

        // Assert invoice line was created
        $this->assertCount(1, $invoice->lines);
        $line = $invoice->lines->first();
        $this->assertEquals('บริการพัฒนาซอฟต์แวร์', $line->description);
        $this->assertEquals(10000.00, $line->subtotal);

        // Assert XML file was generated
        $this->assertTrue(
            Storage::exists('invoices/xml/' . $invoice->invoice_number . '.xml'),
            'XML file was not generated'
        );

        // Assert ledger entries balance
        $entries = LedgerEntry::where('transactionable_type', Invoice::class)
            ->where('transactionable_id', $invoice->id)
            ->get();

        $totalDebits = $entries->sum('debit');
        $totalCredits = $entries->sum('credit');
        $this->assertEquals($totalDebits, $totalCredits, 'Ledger entries do not balance');
        $this->assertEquals(10700.00, $totalDebits, 'Incorrect ledger entry amount');
    }

    protected function tearDown(): void
    {
        Storage::deleteDirectory('invoices/xml');
        Mockery::close();
        parent::tearDown();
    }
}