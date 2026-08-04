<?php

namespace App\Console\Commands;

use App\Services\RssNewsService;
use Illuminate\Console\Command;

class ImportNewsFromRss extends Command
{
    protected $signature = 'news:import-rss';

    protected $description = 'Import the latest articles from configured entertainment news RSS feeds';

    public function handle(RssNewsService $service): int
    {
        $created = $service->import();

        $this->info("Imported {$created} new article(s).");

        return self::SUCCESS;
    }
}
