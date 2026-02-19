<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FailedDeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the report ID mapping
        $mappingPath = storage_path('app/report_id_mapping.json');
        
        if (!file_exists($mappingPath)) {
            $this->command->error("Report ID mapping file not found. Please run ReportSeeder first.");
            return;
        }

        $idMapping = json_decode(file_get_contents($mappingPath), true);

        // Path to your JSON file
        $jsonPath = base_path('int_tracker.faileddeliveries.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        // Read and decode JSON
        $jsonData = file_get_contents($jsonPath);
        $failedDeliveries = json_decode($jsonData, true);

        if (!$failedDeliveries) {
            $this->command->error("Failed to parse JSON file");
            return;
        }

        $this->command->info("Starting failed deliveries import...");
        $imported = 0;
        $skipped = 0;

        foreach ($failedDeliveries as $delivery) {
            try {
                // Parse MongoDB dates
                $createdAt = isset($delivery['createdAt']['$date']) 
                    ? Carbon::parse($delivery['createdAt']['$date']) 
                    : now();
                    
                $updatedAt = isset($delivery['updatedAt']['$date']) 
                    ? Carbon::parse($delivery['updatedAt']['$date']) 
                    : now();

                // Get the MongoDB report ObjectId
                $mongoReportId = $delivery['report']['$oid'];

                // Map to new report_id
                $reportId = $idMapping[$mongoReportId] ?? null;

                if (!$reportId) {
                    $this->command->warn("Report mapping not found for MongoDB ID: {$mongoReportId} - skipping");
                    $skipped++;
                    continue;
                }

                // Insert failed delivery
                DB::table('failed_deliveries')->insert([
                    'report_id' => $reportId,
                    'canceled_bef_delivery' => $delivery['canceled_bef_delivery'] ?? 0,
                    'no_cash_available' => $delivery['no_cash_available'] ?? 0,
                    'postpone' => $delivery['postpone'] ?? 0,
                    'not_at_home' => $delivery['not_at_home'] ?? 0,
                    'refuse' => $delivery['refuse'] ?? 0,
                    'unreachable' => $delivery['unreachable'] ?? 0,
                    'invalid_address' => $delivery['invalid_address'] ?? 0,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $this->command->info("✓ Imported failed delivery for report ID: {$reportId}");
                $imported++;

            } catch (\Exception $e) {
                $this->command->error("Error importing failed delivery: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("\n=== Import Complete ===");
        $this->command->info("Imported: {$imported}");
        $this->command->info("Skipped: {$skipped}");
        $this->command->info("Total: " . count($failedDeliveries));
    }
}
