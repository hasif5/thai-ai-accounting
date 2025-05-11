<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\AssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class MakeAIInvoiceCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAssistantService = Mockery::mock(AssistantService::class);
        $this->app->instance(AssistantService::class, $this->mockAssistantService);
    }

    public function test_it_creates_invoice_from_thai_description(): void
    {
        // Mock AI response
        $mockResponse = json_encode([
            'invoice_number' => 'INV-20230101-001',
            'customer_id' => 1,
            'issue_date' => '2023-01-01',
            'due_date' => '2023-01-15',
            'subtotal' => 1000.00,
            'tax' => 70.00,
            'total' => 1070.00,
            'lines' => [
                [
                    'description' => 'บริการให้คำปรึกษา',
                    'quantity' => 1,
                    'unit_price' => 1000.00,
                    'tax_rate' => 7.00,
                    'tax_amount' => 70.00,
                    'subtotal' => 1000.00,
                    'total' => 1070.00
                ]
            ]
        ]);

        $this->mockAssistantService
            ->shouldReceive('getResponse')
            ->with('invoice_create', ['description' => 'ค่าที่ปรึกษาเดือนมกราคม'])
            ->once()
            ->andReturn($mockResponse);

        // Execute command
        $this->artisan('make:ai-invoice', [
            'description' => 'ค่าที่ปรึกษาเดือนมกราคม'
        ])->assertSuccessful();

        // Assert invoice was created
        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-20230101-001',
            'notes' => 'ค่าที่ปรึกษาเดือนมกราคม'
        ]);

        // Get created invoice
        $invoice = Invoice::where('invoice_number', 'INV-20230101-001')->first();
        
        // Assert invoice line was created
        $this->assertDatabaseHas('invoice_lines', [
            'invoice_id' => $invoice->id,
            'description' => 'บริการให้คำปรึกษา',
            'quantity' => 1.00,
            'unit_price' => 1000.00
        ]);
    }

    public function test_it_handles_invalid_ai_response(): void
    {
        $this->mockAssistantService
            ->shouldReceive('getResponse')
            ->andReturn('invalid json');

        $this->artisan('make:ai-invoice', [
            'description' => 'ค่าที่ปรึกษา'
        ])->assertFailed();

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_lines', 0);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}