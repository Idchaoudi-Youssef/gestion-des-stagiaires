<?php

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Services\InternshipService;

beforeEach(function () {
    $this->service = new InternshipService;
});

test('an internship whose end date has passed is closed', function () {
    $internship = Internship::factory()->overdue()->create();

    $this->service->closeFinishedInternships();

    expect($internship->refresh()->status)->toBe(InternshipStatus::Finished);
});

test('an internship that is still running keeps its status', function () {
    $internship = Internship::factory()->create([
        'end_date' => now()->addMonth()->toDateString(),
        'status' => InternshipStatus::InPeriod->value,
    ]);

    $this->service->closeFinishedInternships();

    expect($internship->refresh()->status)->toBe(InternshipStatus::InPeriod);
});

test('an internship that never started is not closed', function () {
    $internship = Internship::factory()->create([
        'end_date' => now()->subDay()->toDateString(),
        'status' => InternshipStatus::NotStarted->value,
    ]);

    $this->service->closeFinishedInternships();

    expect($internship->refresh()->status)->toBe(InternshipStatus::NotStarted);
});

test('an internship that is already closed is left alone', function () {
    $internship = Internship::factory()->finished()->create();

    expect($this->service->closeFinishedInternships())->toBe(0);
    expect($internship->refresh()->status)->toBe(InternshipStatus::Finished);
});

test('the number of closed internships is returned', function () {
    Internship::factory()->overdue()->count(2)->create();

    expect($this->service->closeFinishedInternships())->toBe(2);
});
