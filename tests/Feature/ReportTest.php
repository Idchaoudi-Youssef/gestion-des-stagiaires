<?php

use App\Enums\InternshipStatus;
use App\Enums\ReportState;
use App\Enums\ReportType;
use App\Models\Internship;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Reports and internship certificate
|--------------------------------------------------------------------------
*/

test('an intern can submit a weekly report to one of their supervisors', function () {
    Storage::fake('public');

    $intern = User::factory()->intern()->create();
    $supervisor = User::factory()->supervisor()->create();
    $intern->supervisors()->attach($supervisor->id);

    $response = $this->actingAs($intern)->post('/intern/reports', [
        'file' => UploadedFile::fake()->create('weekly.pdf', 120, 'application/pdf'),
        'type' => ReportType::Weekly->value,
        'supervisor_id' => $supervisor->id,
    ]);

    $response->assertRedirect(route('intern.reports'));

    $report = Report::where('intern_id', $intern->id)->firstOrFail();

    expect($report->type)->toBe(ReportType::Weekly);
    expect($report->supervisor_id)->toBe($supervisor->id);
    expect($report->state)->toBe(ReportState::Pending);
    Storage::disk('public')->assertExists($report->file);
});

test('an intern can not send a weekly report to a supervisor who is not theirs', function () {
    Storage::fake('public');

    $intern = User::factory()->intern()->create();
    $otherSupervisor = User::factory()->supervisor()->create();

    $this->actingAs($intern)->post('/intern/reports', [
        'file' => UploadedFile::fake()->create('weekly.pdf', 120, 'application/pdf'),
        'type' => ReportType::Weekly->value,
        'supervisor_id' => $otherSupervisor->id,
    ])->assertSessionHasErrors('supervisor_id');

    expect(Report::where('intern_id', $intern->id)->count())->toBe(0);
});

test('a final report has no supervisor and no administrator', function () {
    Storage::fake('public');

    $intern = User::factory()->intern()->create();

    $this->actingAs($intern)->post('/intern/reports', [
        'file' => UploadedFile::fake()->create('final.pdf', 120, 'application/pdf'),
        'type' => ReportType::Final->value,
    ])->assertRedirect(route('intern.reports'));

    $report = Report::where('intern_id', $intern->id)->firstOrFail();

    expect($report->type)->toBe(ReportType::Final);
    expect($report->supervisor_id)->toBeNull();
    expect($report->admin_id)->toBeNull();
});

test('a file that is not a pdf or a word document is refused', function () {
    Storage::fake('public');

    $intern = User::factory()->intern()->create();

    $this->actingAs($intern)->post('/intern/reports', [
        'file' => UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream'),
        'type' => ReportType::Final->value,
    ])->assertSessionHasErrors('file');

    expect(Report::where('intern_id', $intern->id)->count())->toBe(0);
});

test('the certificate is not available while the internship is running', function () {
    $intern = User::factory()->intern()->create();

    Internship::create([
        'intern_id' => $intern->id,
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->addMonth()->toDateString(),
        'status' => InternshipStatus::InPeriod->value,
    ]);

    $this->actingAs($intern)
        ->get('/intern/attestation')
        ->assertOk()
        ->assertSee('Your internship is not yet complete.');
});

test('the certificate appears once the final report is approved', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();

    Internship::create([
        'intern_id' => $intern->id,
        'start_date' => now()->subMonths(6)->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'internship_period' => 6,
        'position' => 'Junior Web Developer',
        'status' => InternshipStatus::Finished->value,
    ]);

    $report = Report::factory()->final()->create(['intern_id' => $intern->id]);

    // The report is still waiting for a decision.
    $this->actingAs($intern)
        ->get('/intern/attestation')
        ->assertOk()
        ->assertSee('Your report is under review.');

    $this->actingAs($admin)->post('/admin/approve-report/'.$report->id);

    $this->actingAs($intern)
        ->get('/intern/attestation')
        ->assertOk()
        ->assertSee('Internship Certificate')
        ->assertSee('Junior Web Developer');
});

test('a rejected report shows the reason to the intern', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();

    Internship::create([
        'intern_id' => $intern->id,
        'start_date' => now()->subMonths(6)->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'status' => InternshipStatus::Finished->value,
    ]);

    $report = Report::factory()->final()->rejected()->create(['intern_id' => $intern->id]);

    $this->actingAs($admin)->post('/admin/reject-report/'.$report->id, [
        'reject_reason' => 'The report is incomplete.',
    ]);

    $this->actingAs($intern)
        ->get('/intern/attestation')
        ->assertOk()
        ->assertSee('Your report has been rejected.')
        ->assertSee('The report is incomplete.');
});
