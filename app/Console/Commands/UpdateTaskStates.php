<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class UpdateTaskStates extends Command
{
    protected $signature = 'tasks:update-states';
    protected $description = 'Update task states based on their limit dates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tasks = Task::where('state', 'Pending')
            ->where('limit_date', '<', Carbon::now())
            ->get();

        foreach ($tasks as $task) {
            $task->state = 'Unfinished';
            $task->save();
        }

        $this->info('Task states updated successfully.');
    }
}
