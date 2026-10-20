<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\TaskStateService;
use Illuminate\Console\Command;

class UpdateTaskStates extends Command
{
    protected $signature = 'tasks:update-states';

    protected $description = 'Flag the pending tasks whose deadline has passed as unfinished';

    public function handle(TaskStateService $taskStates): int
    {
        $updated = $taskStates->flagOverdueTasks(Task::query());

        $this->info("{$updated} task(s) updated.");

        return self::SUCCESS;
    }
}
