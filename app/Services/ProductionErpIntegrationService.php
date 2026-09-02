<?php

namespace App\Services;

use App\Models\ProductionCosting;
use App\Models\Book;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionErpIntegrationService
{
    protected string $baseUrl;
    protected string $apiName;
    protected string $apiKey;
    protected string $tableName;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.production_erp.url', 'https://erpccfi.claretianpublications.ph'), '/');
        $this->apiName = config('services.production_erp.api_name', 'bosing');
        $this->apiKey = config('services.production_erp.api_key', 'T8LS BY8Q pgcm sTc9 aFek O8jJ');
        $this->tableName = config('services.production_erp.table', 'uuqs_ccfi_accounting_handoff');
    }

    /**
     * Sync costings from WordPress Production ERP via REST API or DB handoff table.
     *
     * @return array Summary of synced records count and status.
     */
    public function syncCostings(): array
    {
        $syncedCount = 0;

        // Verify credentials from .env
        if (empty($this->apiName) || empty($this->apiKey)) {
            return [
                'success' => false,
                'source' => 'config_missing',
                'count' => 0,
                'message' => 'Production ERP API credentials missing in .env. Please set PRODUCTION_ERP_API_NAME and PRODUCTION_ERP_API_KEY.'
            ];
        }

        // 1. Try Direct Database Table Query (Same-server database synchronization)
        try {
            if (DB::getSchemaBuilder()->hasTable($this->tableName)) {
                $rows = DB::table($this->tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $this->importOrUpdateFromData((array) $row);
                        $syncedCount++;
                    }
                    return [
                        'success' => true,
                        'source' => 'direct_db',
                        'count' => $syncedCount,
                        'message' => "Successfully synchronized {$syncedCount} production costing records from handoff table ({$this->tableName})."
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::info("Direct DB handoff check skipped or unavailable: " . $e->getMessage());
        }

        // 2. Try REST API Pull via WordPress Application Password Authentication
        try {
            $endpoint = $this->baseUrl . '/wp-json/ccfi-erp/v1/accounting-costs';

            $response = Http::withBasicAuth($this->apiName, $this->apiKey)
                ->withoutVerifying()
                ->timeout(20)
                ->get($endpoint, [
                    'per_page' => 100,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $snapshots = $data['records'] ?? $data['snapshots'] ?? $data['data'] ?? (is_array($data) ? $data : []);

                foreach ($snapshots as $snapshot) {
                    $this->importOrUpdateFromData($snapshot);
                    $syncedCount++;
                }

                $totalAvailable = $data['total'] ?? $syncedCount;

                return [
                    'success' => true,
                    'source' => 'rest_api',
                    'count' => $syncedCount,
                    'message' => "Successfully pulled and synchronized {$syncedCount} (out of {$totalAvailable} total) production costing records directly from WordPress Production ERP!"
                ];
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'HTTP ' . $response->status() . ' ' . $response->reason();
                
                Log::warning("WordPress REST API Authentication Error: " . json_encode($errorData));

                return [
                    'success' => false,
                    'source' => 'rest_api',
                    'count' => 0,
                    'message' => "Production ERP API Error (" . $response->status() . "): " . strip_tags($errorMessage)
                ];
            }
        } catch (\Throwable $e) {
            Log::error("WordPress Production ERP REST API Sync exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'success' => false,
                'source' => 'error',
                'count' => 0,
                'message' => "Sync error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Parse raw WordPress handoff array and insert/update local ProductionCosting record.
     */
    protected function importOrUpdateFromData(array $item): ProductionCosting
    {
        // Support nested structure from WordPress /ccfi-erp/v1/accounting-costs
        $project = $item['project'] ?? [];
        $costs = $item['production_costs'] ?? [];
        $labor = $costs['labor_breakdown'] ?? [];

        $jobNum = $project['protocol'] ?? $item['job_number'] ?? $item['protocol_number'] ?? ('JOB-WP-' . ($project['project_id'] ?? $item['id'] ?? rand(1000, 9999)));
        $title = $project['title'] ?? $item['job_title'] ?? $item['title'] ?? ('Production Project #' . ($project['project_id'] ?? 'Costing'));
        
        $qty = (int) ($project['print_run'] ?? $item['quantity_produced'] ?? $item['run_quantity'] ?? 1000);
        if ($qty <= 0) {
            $qty = 1000;
        }
        $pages = (int) ($item['pages_count'] ?? $item['pages'] ?? 100);

        // Extract 12 Cost Components
        $paperCost      = (float) ($costs['materials_total'] ?? $item['paper_cost'] ?? $item['paper'] ?? 0);
        $inkCost        = (float) ($item['ink_cost'] ?? $item['ink'] ?? 0);
        $laborCost      = (float) ($costs['direct_labor_total'] ?? $item['labor_cost'] ?? $item['labor'] ?? 0);
        $electricityCost= (float) ($costs['energy_electricity'] ?? $item['electricity_cost'] ?? $item['electricity'] ?? 0);
        $machineCost    = (float) ($costs['manufacturing_overhead'] ?? $item['machine_cost'] ?? $item['machine'] ?? 0);
        $bindingCost    = (float) ($labor['cutting_binding_finishing'] ?? $item['binding_cost'] ?? $item['binding'] ?? 0);
        $uvCost         = (float) ($costs['finishing_total'] ?? $item['uv_cost'] ?? $item['uv'] ?? 0);
        $shrinkWrapCost = (float) ($item['shrink_wrap_cost'] ?? $item['shrink_wrap'] ?? 0);
        $packagingCost  = (float) ($labor['packing_warehouse'] ?? $item['packaging_cost'] ?? $item['packaging'] ?? 0);
        $freightCost    = (float) ($costs['delivery_handling'] ?? $item['freight_cost'] ?? $item['freight'] ?? 0);
        $warehouseCost  = (float) ($item['warehouse_cost'] ?? $item['warehouse'] ?? 0);
        $overheadCost   = (float) ($costs['manufacturing_overhead'] ?? $item['overhead_cost'] ?? $item['overhead'] ?? 0);

        // Calculate or read COGS
        $totalCogs = (float) ($costs['production_cogs_total'] ?? $costs['total_cogs'] ?? $item['total_cogs'] ?? (
            $paperCost + $inkCost + $laborCost + $electricityCost + $machineCost
            + $bindingCost + $uvCost + $shrinkWrapCost + $packagingCost
            + $freightCost + $warehouseCost + $overheadCost
        ));

        $unitCogs = (float) ($costs['cogs_per_copy'] ?? ($qty > 0 ? round($totalCogs / $qty, 2) : 0.00));

        // Try linking to catalog book by SKU if present
        $bookId = null;
        if (!empty($project['protocol']) || !empty($item['isbn'])) {
            $identifier = $project['protocol'] ?? $item['isbn'] ?? '';
            $book = Book::where('sku', $identifier)->first();
            if ($book) {
                $bookId = $book->id;
            }
        }

        return ProductionCosting::updateOrCreate(
            ['job_number' => $jobNum],
            [
                'book_id' => $bookId,
                'job_title' => $title,
                'quantity_produced' => $qty,
                'pages_count' => $pages,
                'paper_cost' => $paperCost,
                'ink_cost' => $inkCost,
                'labor_cost' => $laborCost,
                'electricity_cost' => $electricityCost,
                'machine_cost' => $machineCost,
                'binding_cost' => $bindingCost,
                'uv_cost' => $uvCost,
                'shrink_wrap_cost' => $shrinkWrapCost,
                'packaging_cost' => $packagingCost,
                'freight_cost' => $freightCost,
                'warehouse_cost' => $warehouseCost,
                'overhead_cost' => $overheadCost,
                'total_cogs' => round($totalCogs, 2),
                'unit_cogs' => $unitCogs,
                'status' => 'calculated',
                'notes' => 'Synced automatically from WordPress Production ERP (erpccfi.claretianpublications.ph). Protocol: ' . ($project['protocol'] ?? $jobNum),
            ]
        );
    }
}
