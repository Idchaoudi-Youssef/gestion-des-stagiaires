<?php

namespace App\Console\Commands;

use App\Services\InternshipService;
use Illuminate\Console\Command;

class CloseFinishedInternships extends Command
{
    protected $signature = 'internships:close-finished';

    protected $description = 'Move the internships whose end date has passed to the finished state';

    public function handle(InternshipService $internships): int
    {
        $updated = $internships->closeFinishedInternships();

        $this->info("{$updated} internship(s) updated.");

        return self::SUCCESS;
    }
}
