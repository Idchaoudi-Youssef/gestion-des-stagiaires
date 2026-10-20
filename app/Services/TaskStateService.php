<?php

namespace App\Services;

use App\Enums\TaskState;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Keeps the state of the tasks in sync with their deadline.
 *
 * The rule is simple and was broken before:
 * a task whose deadline has passed becomes "unfinished", but a task that has
 * already been completed by the intern is never touched.
 */
class TaskStateService
{
    /**
     * Flags every pending task of the given query as unfinished.
     *
     * @param  Builder<Task>  $query
     * @return int the number of tasks that were updated
     */
    public function flagOverdueTasks(Builder $query): int
    {
        return $query
            ->where('state', TaskState::Pending->value)
            ->whereNotNull('limit_date')
            ->where('limit_date', '<', now())
            ->update(['state' => TaskState::Unfinished->value]);
    }

    /**
     * Flags the overdue tasks of a collection of tasks.
     *
     * @param  Collection<int, Task>  $tasks
     * @return int the number of tasks that were updated
     */
    public function flagOverdueTasksIn(Collection $tasks): int
    {
        $updated = 0;

        foreach ($tasks as $task) {
            if (! $task->isOverdue()) {
                continue;
            }

            $task->state = TaskState::Unfinished;
            $task->save();
            $updated++;
        }

        return $updated;
    }
}
