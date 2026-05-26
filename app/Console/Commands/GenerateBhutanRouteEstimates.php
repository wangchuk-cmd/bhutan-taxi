<?php

namespace App\Console\Commands;

use App\Services\DzongkhagRouteEstimator;
use Illuminate\Console\Command;

class GenerateBhutanRouteEstimates extends Command
{
    protected $signature = 'routes:estimate-bhutan {--only-missing : Create only missing route pairs}';

    protected $description = 'Generate Bhutan dzongkhag routes with estimated distance (km) and travel time (HH:MM)';

    public function handle(DzongkhagRouteEstimator $estimator): int
    {
        $dzongkhags = config('dzongkhags.list', []);

        if (empty($dzongkhags)) {
            $this->error('No dzongkhags found in config/dzongkhags.php');
            return self::FAILURE;
        }

        $onlyMissing = (bool) $this->option('only-missing');
        $summary = $estimator->syncAllRoutes($dzongkhags, $onlyMissing);

        $this->info('Bhutan route estimate generation complete.');
        $this->line("Created: {$summary['created']}");
        $this->line("Updated: {$summary['updated']}");
        $this->line("Skipped: {$summary['skipped']}");

        return self::SUCCESS;
    }
}
