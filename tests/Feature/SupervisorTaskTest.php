<?php

use App\Enums\TaskState;
use App\Models\Task;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Tasks of the supervisor
|--------------------------------------------------------------------------
*/

test('a supervisor can give a task to one of their interns', function () {
    $supervisor = User::factory()->supervisor()->create();
    $intern = User::factory()->intern()->create();
    $supervisor->interns()->attach($intern->id);

    $response = $this->actingAs($supervisor)->post('/supervisor/tasks', [
        'title' => 'Write the documentation',
        'description' => 'Document the new endpoints.',
        'limit_date' => now()->addWeek()->toDateTimeString(),
        'intern_id' => $intern->id,
    ]);

    $response->assertRedirect(route('supervisor.tasks'));

    $this->assertDatabaseHas('tasks', [
        'title' => 'Write the documentation',
        'intern_id' => $intern->id,
        'supervisor_id' => $supervisor->id,
        'state' => TaskState::Pending->value,
    ]);
});

test('a supervisor can not give a task to an intern that is not theirs', function () {
    $supervisor = User::factory()->supervisor()->create();
    $otherIntern = User::factory()->intern()->create();

    $response = $this->actingAs($supervisor)->post('/supervisor/tasks', [
        'title' => 'Write the documentation',
        'description' => 'Document the new endpoints.',
        'limit_date' => now()->addWeek()->toDateTimeString(),
        'intern_id' => $otherIntern->id,
    ]);

    $response->assertSessionHasErrors('intern_id');
    $this->assertDatabaseMissing('tasks', ['title' => 'Write the documentation']);
});

test('a supervisor can not edit the task of another supervisor', function () {
    $supervisor = User::factory()->supervisor()->create();
    $otherSupervisor = User::factory()->supervisor()->create();
    $task = Task::factory()->create(['supervisor_id' => $otherSupervisor->id]);

    $this->actingAs($supervisor)
        ->put('/supervisor/tasks/'.$task->id, [
            'title' => 'Hijacked',
            'description' => 'Hijacked',
            'limit_date' => now()->addWeek()->toDateTimeString(),
            'intern_id' => $task->intern_id,
        ])
        ->assertForbidden();

    expect($task->refresh()->title)->not->toBe('Hijacked');
});

test('a supervisor can not delete the task of another supervisor', function () {
    $supervisor = User::factory()->supervisor()->create();
    $otherSupervisor = User::factory()->supervisor()->create();
    $task = Task::factory()->create(['supervisor_id' => $otherSupervisor->id]);

    $this->actingAs($supervisor)
        ->delete('/supervisor/tasks/'.$task->id)
        ->assertForbidden();

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('a supervisor can delete their own task', function () {
    $supervisor = User::factory()->supervisor()->create();
    $task = Task::factory()->create(['supervisor_id' => $supervisor->id]);

    $this->actingAs($supervisor)
        ->delete('/supervisor/tasks/'.$task->id)
        ->assertRedirect(route('supervisor.tasks'));

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('the task list of a supervisor only contains their own tasks', function () {
    $supervisor = User::factory()->supervisor()->create();
    $otherSupervisor = User::factory()->supervisor()->create();

    Task::factory()->create(['supervisor_id' => $supervisor->id, 'title' => 'My task']);
    Task::factory()->create(['supervisor_id' => $otherSupervisor->id, 'title' => 'Their task']);

    $response = $this->actingAs($supervisor)->get('/supervisor/tasks');

    $response->assertOk();
    $response->assertSee('My task');
    $response->assertDontSee('Their task');
});
