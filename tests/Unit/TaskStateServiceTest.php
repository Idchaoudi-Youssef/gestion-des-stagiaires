<?php

use App\Enums\TaskState;
use App\Models\Task;
use App\Services\TaskStateService;

/*
|--------------------------------------------------------------------------
| Task states
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->service = new TaskStateService;
});

test('a pending task whose deadline has passed becomes unfinished', function () {
    $task = Task::factory()->overdue()->create();

    $this->service->flagOverdueTasks(Task::query());

    expect($task->refresh()->state)->toBe(TaskState::Unfinished);
});

test('a completed task is never sent back to unfinished', function () {
    $task = Task::factory()->overdue()->completed()->create();

    $this->service->flagOverdueTasks(Task::query());

    expect($task->refresh()->state)->toBe(TaskState::Completed);
});

test('a task whose deadline is still in the future keeps its state', function () {
    $task = Task::factory()->create();

    $this->service->flagOverdueTasks(Task::query());

    expect($task->refresh()->state)->toBe(TaskState::Pending);
});

test('a task without a deadline keeps its state', function () {
    $task = Task::factory()->create(['limit_date' => null]);

    $this->service->flagOverdueTasks(Task::query());

    expect($task->refresh()->state)->toBe(TaskState::Pending);
});

test('the number of updated tasks is returned', function () {
    Task::factory()->overdue()->count(3)->create();
    Task::factory()->overdue()->completed()->create();

    $updated = $this->service->flagOverdueTasks(Task::query());

    expect($updated)->toBe(3);
});

test('the same rule applies to a collection of tasks', function () {
    $overdue = Task::factory()->overdue()->create();
    $completed = Task::factory()->overdue()->completed()->create();

    $updated = $this->service->flagOverdueTasksIn(Task::all());

    expect($updated)->toBe(1);
    expect($overdue->refresh()->state)->toBe(TaskState::Unfinished);
    expect($completed->refresh()->state)->toBe(TaskState::Completed);
});
