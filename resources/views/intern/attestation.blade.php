@extends('layouts.dashboard')

@section('main-content')
<div class="print-container">
    <div class="container">
        <div class="header">

            <div class="logo" >
                <img src="{{ asset('img/logo-placeholder.svg') }}" alt="Logo de l'entreprise" >
            </div>
            <div class="company-info">
                <h4>Company Name</h4>
                <h4>City</h4>
                <h4>Téléphone: 00 00 00 00 00</h4>
                <h4>Email: contact@example.com</h4>
            </div>
        </div>
        <div class="title">
            <h1>Attestation de Stage</h1>
        </div>
        <div class="content">
            <p>Nous, soussignés, <strong>Nom de l'Entreprise</strong>, attestons par la présente que :</p>
            <p><strong>Nom du Stagiaire</strong></p>
            <p>A effectué un stage au sein de notre entreprise du <strong>date de début</strong> au <strong>date de fin</strong>.</p>
            <p>Durant cette période, <strong>Nom du Stagiaire</strong> a occupé le poste de <strong>poste occupé</strong> et a participé aux missions suivantes :</p>
            <ul>
                <li>Mission 1</li>
                <li>Mission 2</li>
                <li>Mission 3</li>
            </ul>
            <p>Le stagiaire a fait preuve de <strong>compétences particulières</strong> et a su s'intégrer rapidement au sein de notre équipe.</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary print-button">Print Attestation</button>

        <div class="signature">
            <p>Fait à <strong>City</strong>, le <strong>date</strong></p>
            <p><strong>Admin</strong></p>
            <p><strong>Directeur de l'entreprise</strong></p>
            <br>
            <img src="{{ asset('path/to/signature.png') }}" alt="Signature">
        </div>
    </div>
</div>

<style>
    .container {
        border: none;
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom:170px; /* Added margin between header and content */
    }
    .header img {
        max-height: 150px;
    }
    .title {
        text-align: center;
        margin-top: 20px;
    }
    .content {
        margin-top: 40px;
        line-height: 1.6;
        margin-bottom: 370px;
    }
    .signature {
        text-align: center;
        display: none; /* Hide signature by default */
    }
    .signature img {
        max-height: 80px;
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
    /* Adjust font size for printed version */
    .content,
    .content p,
    .content ul,
    .content li {
        font-size: 26px; /* Adjust the font size as needed */
    }
}

</style>


@endsection
