@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Attestation</h1>
    <p>Name: {{ $intern->name }}</p>
    <p>Email: {{ $intern->email }}</p>
    <!-- Add more details as needed -->
    <button onclick="window.print()" class="btn btn-primary">Print Attestation</button>
</div>
@endsection
