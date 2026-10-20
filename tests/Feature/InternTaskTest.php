<?php

use App\Enums\TaskState;
use App\Models\Task;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tasks of the intern
|--------------------------------------------------------------------------
*/

test('an intern can complete a task that belongs to them', function () {
    $intern = User::factory()->intern()->create();
    $task = Task::factory()->create(['intern_id' => $intern->id]);

    $response = $this->actingAs($intern)->put('/tasks/'.$task->id.'/complete');

    $response->assertRedirect();
    expect($task->refresh()->state)->toBe(TaskState::Completed);
});

test('an intern can not complete a task that belongs to somebody else', function () {
    $intern = User::factory()->intern()->create();
    $otherIntern = User::factory()->intern()->create();
    $task = Task::factory()->create(['intern_id' => $otherIntern->id]);

    $this->actingAs($intern)
        ->put('/tasks/'.$task->id.'/complete')
        ->assertForbidden();

    expect($task->refresh()->state)->toBe(TaskState::Pending);
});

test('an intern can not complete a task that is already completed', function () {
    $intern = User::factory()->intern()->create();
    $task = Task::factory()->completed()->create(['intern_id' => $intern->id]);

    $this->actingAs($intern)
        ->put('/tasks/'.$task->id.'/complete')
        ->assertForbidden();

    expect($task->refresh()->state)->toBe(TaskState::Completed);
});

test('a supervisor can not complete a task', function () {
    $supervisor = User::factory()->supervisor()->create();
    $task = Task::factory()->create(['supervisor_id' => $supervisor->id]);

    $this->actingAs($supervisor)
        ->put('/tasks/'.$task->id.'/complete')
        ->assertForbidden();
});

test('the search only returns the tasks of the logged in intern', function () {
    $intern = User::factory()->intern()->create();
    $otherIntern = User::factory()->intern()->create();

    Task::factory()->create(['intern_id' => $intern->id, 'title' => 'Secret report']);
    Task::factory()->create(['intern_id' => $otherIntern->id, 'title' => 'Secret report']);

    $response = $this->actingAs($intern)->get('/tasks/search?query=Secret');

    $response->assertOk();
    $response->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 1);
    $response->assertSee('Secret report');
});

test('the filter only returns the tasks of the logged in intern', function () {
    $intern = User::factory()->intern()->create();
    $otherIntern = User::factory()->intern()->create();
    $supervisor = User::factory()->supervisor()->create();

    Task::factory()->create([
        'intern_id' => $intern->id,
        'supervisor_id' => $supervisor->id,
        'title' => 'My own task',
    ]);
    Task::factory()->create([
        'intern_id' => $otherIntern->id,
        'supervisor_id' => $supervisor->id,
        'title' => 'Somebody else task',
    ]);

    $response = $this->actingAs($intern)->get('/tasks/filter?supervisor='.$supervisor->id);

    $response->assertOk();
    $response->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 1);
    $response->assertSee('My own task');
    $response->assertDontSee('Somebody else task');
});

test('the intern dashboard counts the tasks with the right state', function () {
    $intern = User::factory()->intern()->create();

    Task::factory()->create(['intern_id' => $intern->id]);
    Task::factory()->completed()->create(['intern_id' => $intern->id]);
    Task::factory()->overdue()->create(['intern_id' => $intern->id]);

    $response = $this->actingAs($intern)->get('/intern/dashboard');

    $response->assertOk();
    $response->assertViewHas('pendingTasks', 1);
    $response->assertViewHas('completedTasks', 1);
    $response->assertViewHas('unfinishedTasks', 1);
});

test('an overdue task is flagged as unfinished and a completed task is left alone', function () {
    $intern = User::factory()->intern()->create();

    $overdue = Task::factory()->overdue()->create(['intern_id' => $intern->id]);
    $completed = Task::factory()->overdue()->completed()->create(['intern_id' => $intern->id]);

    $this->actingAs($intern)->get('/intern/tasks');

    expect($overdue->refresh()->state)->toBe(TaskState::Unfinished);
    expect($completed->refresh()->state)->toBe(TaskState::Completed);
});
