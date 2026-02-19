<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $jsonPath = base_path('int_tracker.reports.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        // Read and decode JSON
        $jsonData = file_get_contents($jsonPath);
        $reports = json_decode($jsonData, true);

        if (!$reports) {
            $this->command->error("Failed to parse JSON file");
            return;
        }

        $this->command->info("Starting report import...");
        $imported = 0;
        $skipped = 0;

        // Store mapping of MongoDB ObjectId to new auto-increment ID
        $idMapping = [];

        foreach ($reports as $report) {
            try {
                // Parse MongoDB dates
                $createdAt = isset($report['createdAt']['$date']) 
                    ? Carbon::parse($report['createdAt']['$date']) 
                    : now();
                    
                $updatedAt = isset($report['updatedAt']['$date']) 
                    ? Carbon::parse($report['updatedAt']['$date']) 
                    : now();

                // Get the MongoDB ObjectId
                $mongoId = $report['_id']['$oid'];

                // Get hub_id by name (assuming hub was already seeded)
                $hubId = null;
                if (isset($report['hub'])) {
                    $hub = DB::table('hub')->where('name', $report['hub'])->first();
                    if ($hub) {
                        $hubId = $hub->id;
                    } else {
                        $this->command->warn("Hub '{$report['hub']}' not found - skipping report");
                        $skipped++;
                        continue;
                    }
                }

                // Get user_id by name (assuming users exist)
                $userId = null;
                if (isset($report['rider'])) {
                    $user = DB::table('users')->where('name', $report['rider'])->first();
                    if ($user) {
                        $userId = $user->id;
                    }
                }

                // Insert report
                $newId = DB::table('reports')->insertGetId([
                    'date' => Carbon::parse($report['date']['$date']),
                    'hub_id' => $hubId,
                    'user_id' => $userId,
                    'client' => $report['client'] ?? null,
                    'total_success' => $report['total_success'] ?? 0,
                    'total_failed' => $report['total_failed'] ?? 0,
                    'total_package' => $report['total_package'] ?? 0,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                // Store the mapping
                $idMapping[$mongoId] = $newId;

                $this->command->info("✓ Imported report ID: {$newId} (MongoDB: {$mongoId})");
                $imported++;

            } catch (\Exception $e) {
                $this->command->error("Error importing report: " . $e->getMessage());
                $skipped++;
            }
        }

        // Store the mapping for use by other seeders
        file_put_contents(
            storage_path('app/report_id_mapping.json'),
            json_encode($idMapping, JSON_PRETTY_PRINT)
        );

        $this->command->info("\n=== Import Complete ===");
        $this->command->info("Imported: {$imported}");
        $this->command->info("Skipped: {$skipped}");
        $this->command->info("Total: " . count($reports));
        $this->command->info("ID mapping saved to: storage/app/report_id_mapping.json");
    }
}
