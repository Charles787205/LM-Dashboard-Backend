<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $jsonPath = base_path('int_tracker.hubs.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        // Read and decode JSON
        $jsonData = file_get_contents($jsonPath);
        $hubs = json_decode($jsonData, true);

        if (!$hubs) {
            $this->command->error("Failed to parse JSON file");
            return;
        }

        // Client mapping
        $clientMap = [
            'LEX' => 1,
            'SPX' => 2,
            '2GO' => 3,
        ];

        $this->command->info("Starting hub import...");
        $imported = 0;
        $skipped = 0;

        foreach ($hubs as $hub) {
            try {
                // Get client_id from mapping
                $clientId = $clientMap[$hub['client']] ?? null;

                if (!$clientId) {
                    $this->command->warn("Unknown client '{$hub['client']}' for hub '{$hub['name']}' - skipping");
                    $skipped++;
                    continue;
                }

                // Parse MongoDB dates
                $createdAt = isset($hub['createdAt']['$date']) 
                    ? Carbon::parse($hub['createdAt']['$date']) 
                    : now();
                    
                $updatedAt = isset($hub['updatedAt']['$date']) 
                    ? Carbon::parse($hub['updatedAt']['$date']) 
                    : now();

                // Check if hub already exists
                $exists = DB::table('hub')
                    ->where('name', $hub['name'])
                    ->where('client_id', $clientId)
                    ->exists();

                if ($exists) {
                    $this->command->warn("Hub '{$hub['name']}' already exists - skipping");
                    $skipped++;
                    continue;
                }

                // Insert hub
                DB::table('hub')->insert([
                    'name' => $hub['name'],
                    'client_id' => $clientId,
                    'hub_lead_id' => null, // Set manually later if needed
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $this->command->info("✓ Imported: {$hub['name']} (Client: {$hub['client']})");
                $imported++;

            } catch (\Exception $e) {
                $this->command->error("Error importing hub '{$hub['name']}': " . $e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("\n=== Import Complete ===");
        $this->command->info("Imported: {$imported}");
        $this->command->info("Skipped: {$skipped}");
        $this->command->info("Total: " . count($hubs));
    }
}
