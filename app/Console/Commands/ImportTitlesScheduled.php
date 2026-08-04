<?php

namespace App\Console\Commands;

use App\Services\TitleImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportTitlesScheduled extends Command
{
    protected $signature = 'titles:import-scheduled';

    protected $description = 'Import a small, balanced batch of trending titles — meant to run on a schedule so the catalogue keeps growing without a manual trigger';

    /**
     * Deliberately small — this runs unattended (or piggybacks on whatever
     * schedule the user has running), so it should never feel like a heavy
     * background job. The admin panel's "Import titles" button is there for
     * anything bigger.
     */
    private const BATCH_SIZE = 8;

    public function handle(TitleImportService $service): int
    {
        $plan = $service->plan(['movie', 'series', 'anime', 'game'], null, 'trending', self::BATCH_SIZE);

        $created = 0;
        foreach ($plan as $candidate) {
            if ($service->importOne($candidate)) {
                $created++;
            }
        }

        Cache::forever('last_scheduled_title_import', [
            'count' => $created,
            'at' => now()->toIso8601String(),
        ]);

        $this->info("Imported {$created} new title(s).");

        return self::SUCCESS;
    }
}
