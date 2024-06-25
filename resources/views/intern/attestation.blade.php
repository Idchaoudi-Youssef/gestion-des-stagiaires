@extends('layouts.dashboard')

@section('main-content')
<div class="print-container">
    <div class="container">

        @if (\Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($internship->end_date)))
        <div class="content">
            <div class="centered">
                <img src="{{ asset('road.png') }}" alt="Image">
                <h1>Your internship is not yet complete.</h1>
                <p>The internship certificate will be available once the internship is completed.</p>
                <a href="{{ route('intern.dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
            </div>
        </div>
        @elseif ($report)
            @if ($report->state === 'approved')
            <div class="attestation">
                <div class="header">
                    <div class="logo">
                        <img src="{{ asset('img/logo-placeholder.svg') }}" alt="Company Logo">
                    </div>
                    <div class="company-info">
                        <h4>Company Name</h4>
                        <h4>City</h4>
                        <h4>Phone: 00 00 00 00 00</h4>
                        <h4>Email: contact@example.com</h4>
                    </div>
                </div>
                <div class="title">
                    <h1>Internship Certificate</h1>
                </div>
                <div class="content">
                    <p>I, the undersigned, Mr. <strong>Signatory Name</strong>, acting as <strong>{{ $signatory_position }}</strong> in the company <strong>Company Name</strong>, certify that <strong>{{ $user->name }}</strong>, residing at <strong>{{ $user->address }}</strong>, completed an internship lasting <strong>{{ $internship->internship_period }} months</strong> at our company <strong>Company Name</strong> from <strong>{{ \Carbon\Carbon::parse($internship->start_date)->format('d/m/Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($internship->end_date)->format('d/m/Y') }}</strong>, in the position of <strong>{{ $internship->position }}</strong> in the <strong>{{ $user->department->name }}</strong> department.</p>
                    <p>This certificate is issued to the interested party to serve and certify as needed.</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary print-button">Print Certificate</button>
                <div class="signature">
                    <p>Done at <strong>City</strong>, on <strong>{{ \Carbon\Carbon::now('Europe/London')->format('d/m/Y') }}</strong></p>
                    <p><strong>Signatory Name</strong></p>
                    <p><strong>Company Director</strong></p>
                    <br>
                    <img src="{{ asset('img/logo-placeholder.svg') }}" alt="Signature">
                </div>
            </div>
            @elseif ($report->state === 'rejected')
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('aaaaa.png') }}" alt="Image">
                    <h1>Your report has been rejected.</h1>
                    <p>The Intern certificate will be available once the report is approved</p>
                    <a href="{{ route('intern.reports.create') }}" class="btn btn-primary">Submit new report</a>
                </div>
            </div>
            @elseif ($report->state === 'pending')
            <div class="content">
                <div class="centered">
                    <img src="{{ asset('aaaaa.png') }}" alt="Image">
                    <h1>Your report is under review.</h1>
                    <p>The Intern certificate will be available once the report is approved</p>
                    <a href="{{ route('intern.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
            @endif
        @elseif($report === null)
        <div class="content">
            <div class="centered">
                <img src="{{ asset('aaaaa.png') }}" alt="Image">
                <h1>You have not submitted your report.</h1>
                <p>The internship certificate will be available once the report is approved.</p>
                <a href="{{ route('intern.reports.create') }}" class="btn btn-primary">Submit your report</a>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .centered {
        text-align: center;
        margin-top: 50px; /* Adjust the top margin as needed */
    }

    .centered img {
        max-width: 50%;
        height: auto;
        margin-bottom: 20px; /* Adjust the bottom margin as needed */
    }

    .container {
        border: none;
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
    .attestation {
        border: 1px solid #000; /* Add border around attestation */
        padding: 20px;
        margin: 20px auto;
        max-width: 600px; /* Adjust the width as needed */
        background-color: #f9f9f9;
        font-size: 14px; /* Adjust font size for screen */
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px; /* Adjusted margin */
    }
    .header img {
        max-height: 80px; /* Adjusted size */
    }
    .title {
        text-align: center;
        margin-top: 10px; /* Adjusted margin */
    }
    .content {
        margin-top: 20px; /* Adjusted margin */
        line-height: 1.4; /* Adjusted line-height */
    }
    .signature {
        text-align: center;
        display: none; /* Hide signature by default */
    }
    .signature img {
        max-height: 50px; /* Adjusted size */
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
            max-width: 800px; /* Original width for print */
            font-size: 26px; /* Original font size for print */
            padding: 40px; /* Original padding for print */
            margin: auto;
            border: none; /* Remove border for print */
        }
        .header {
            margin-bottom: 170px; /* Original margin for print */
        }
        .header img {
            max-height: 150px; /* Original size for print */
        }
        .title {
            margin-top: 20px; /* Original margin for print */
        }
        .content {
            margin-top: 30px; /* Original margin for print */
            line-height: 1.6; /* Original line-height for print */
            margin-bottom: 370px; /* Original margin for print */
        }
        .signature img {
            max-height: 80px; /* Original size for print */
        }
    }
</style>
@endsection
