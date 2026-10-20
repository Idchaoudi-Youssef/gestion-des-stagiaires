<?php

namespace App\Services;

use App\Enums\InternshipStatus;
use App\Models\Internship;

/**
 * Closes the internships whose end date has passed.
 *
 * This used to run as a side effect of loading the administrator dashboard,
 * which meant the status was only refreshed when somebody happened to open
 * that page. It is now an explicit command that can be scheduled.
 */
class InternshipService
{
    /**
     * Moves every internship that is over to the "finished" state.
     *
     * @return int the number of internships that were updated
     */
    public function closeFinishedInternships(): int
    {
        return Internship::query()
            ->where('status', InternshipStatus::InPeriod->value)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->toDateString())
            ->update(['status' => InternshipStatus::Finished->value]);
    }
}
