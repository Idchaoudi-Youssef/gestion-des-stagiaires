@extends('layouts.dashboard')

@section('main-content')
<div class="print-container">
    <div class="container">

        @php
            $company = config('internship.company');
            $signatory = config('internship.signatory');
            $internshipOver = $internship && $internship->end_date && now()->gte($internship->end_date);
        @endphp

        @unless ($internshipOver)
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('road.png') }}" alt="">
                    <h1>Your internship is not yet complete.</h1>
                    <p>The internship certificate will be available once the internship is completed.</p>
                    <a href="{{ route('intern.dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
                </div>
            </div>
        @elseif ($report && $report->state === \App\Enums\ReportState::Approved)
            <div class="attestation">
                <div class="header">
                    <div class="logo">
                        <img src="{{ asset($company['logo']) }}" alt="{{ $company['name'] }} logo">
                    </div>
                    <div class="company-info">
                        <h4>{{ $company['name'] }}</h4>
                        @if ($company['city'])
                            <h4>{{ $company['city'] }}</h4>
                        @endif
                        @if ($company['phone'])
                            <h4>Phone: {{ $company['phone'] }}</h4>
                        @endif
                        @if ($company['email'])
                            <h4>Email: {{ $company['email'] }}</h4>
                        @endif
                        @if ($company['siret'])
                            <h4>SIRET: {{ $company['siret'] }}</h4>
                        @endif
                    </div>
                </div>
                <div class="title">
                    <h1>Internship Certificate</h1>
                </div>
                <div class="content">
                    <p>
                        @if ($signatory['name'] || $signatory['position'])
                            I, the undersigned,
                            @if ($signatory['name'])<strong>{{ $signatory['name'] }}</strong>@endif
                            @if ($signatory['position'])
                                acting as <strong>{{ $signatory['position'] }}</strong>
                            @endif
                            in the company <strong>{{ $company['name'] }}</strong>,
                        @endif
                        certify that <strong>{{ $user->name }}</strong>,
                        residing at <strong>{{ $user->address }}</strong>, completed an internship lasting
                        <strong>{{ $internship->internship_period }} months</strong> at our company
                        {{ $company['name'] }} from
                        <strong>{{ $internship->start_date?->format('d/m/Y') ?? 'an unknown date' }}</strong> to
                        <strong>{{ $internship->end_date->format('d/m/Y') }}</strong>,
                        in the position of <strong>{{ $internship->position ?? 'Intern' }}</strong>
                        in the <strong>{{ $user->department?->name ?? 'general' }}</strong> department.
                    </p>
                    <p>This certificate is issued to the interested party to serve and certify as needed.</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary print-button">Print Certificate</button>
                <div class="signature">
                    @if ($company['city'])
                        <p>Done at <strong>{{ $company['city'] }}</strong>, on <strong>{{ now()->format('d/m/Y') }}</strong></p>
                    @else
                        <p>Done on <strong>{{ now()->format('d/m/Y') }}</strong></p>
                    @endif
                    @if ($signatory['name'])
                        <p><strong>{{ $signatory['name'] }}</strong></p>
                    @endif
                    @if ($signatory['position'])
                        <p><strong>{{ $signatory['position'] }}</strong></p>
                    @endif
                    @if ($signatory['image'])
                        <br>
                        <img src="{{ asset($signatory['image']) }}" alt="Signature">
                    @endif
                </div>
            </div>
        @elseif ($report && $report->state === \App\Enums\ReportState::Rejected)
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('aaaaa.png') }}" alt="">
                    <h1>Your report has been rejected.</h1>
                    <p>
                        @if ($report->reject_reason)
                            Reason: {{ $report->reject_reason }}
                        @endif
                    </p>
                    <p>The internship certificate will be available once the report is approved.</p>
                    <a href="{{ route('intern.reports.create') }}" class="btn btn-primary">Submit a new report</a>
                </div>
            </div>
        @elseif ($report && $report->state === \App\Enums\ReportState::Pending)
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('aaaaa.png') }}" alt="">
                    <h1>Your report is under review.</h1>
                    <p>The internship certificate will be available once the report is approved.</p>
                    <a href="{{ route('intern.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        @else
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('aaaaa.png') }}" alt="">
                    <h1>You have not submitted your report.</h1>
                    <p>The internship certificate will be available once the report is approved.</p>
                    <a href="{{ route('intern.reports.create') }}" class="btn btn-primary">Submit your report</a>
                </div>
            </div>
        @endunless
    </div>
</div>

<style>
    .centered {
        text-align: center;
        margin-top: 50px;
    }

    .centered img {
        max-width: 50%;
        height: auto;
        margin-bottom: 20px;
    }

    .container {
        border: none;
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
    .attestation {
        border: 1px solid #000;
        padding: 20px;
        margin: 20px auto;
        max-width: 600px;
        background-color: #f9f9f9;
        font-size: 14px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .header img {
        max-height: 80px;
    }
    .title {
        text-align: center;
        margin-top: 10px;
    }
    .content {
        margin-top: 20px;
        line-height: 1.4;
    }
    .signature {
        text-align: center;
        display: none;
    }
    .signature img {
        max-height: 50px;
    }
    .print-button {
        display: block;
        margin: 20px auto;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .print-container, .print-container * {
            visibility: visible;
        }
        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print-button {
            display: none;
        }
        .not-for-print {
            display: none;
        }
        .signature {
            display: block;
        }
        .attestation {
            max-width: 800px;
            font-size: 26px;
            padding: 40px;
            margin: auto;
            border: none;
        }
        .header {
            margin-bottom: 170px;
        }
        .header img {
            max-height: 150px;
        }
        .title {
            margin-top: 20px;
        }
        .content {
            margin-top: 30px;
            line-height: 1.6;
            margin-bottom: 370px;
        }
        .signature img {
            max-height: 80px;
        }
    }
</style>
@endsection
