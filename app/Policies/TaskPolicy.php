<?php

namespace App\Policies;

use App\Enums\TaskState;
use App\Enums\UserType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who is allowed to do what with a task.
 *
 * - a supervisor owns the tasks they created and can edit or delete them;
 * - an intern can mark as completed only the tasks that were given to them.
 */
class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Only the supervisor who created the task may update it.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->usertype === UserType::Supervisor
            && $user->id === $task->supervisor_id;
    }

    /**
     * Only the supervisor who created the task may delete it.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->usertype === UserType::Supervisor
            && $user->id === $task->supervisor_id;
    }

    /**
     * An intern may complete a task only when the task was assigned to them
     * and when the task is still waiting.
     */
    public function complete(User $user, Task $task): bool
    {
        return $user->usertype === UserType::Intern
            && $user->id === $task->intern_id
            && $task->state === TaskState::Pending;
    }
}
