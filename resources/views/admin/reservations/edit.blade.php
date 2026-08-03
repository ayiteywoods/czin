@extends('layouts.admin')

@section('heading', 'Edit reservation')

@section('content')
    <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.reservations.partials.form', ['reservation' => $reservation])
        <button type="submit" class="btn-primary">Update reservation</button>
    </form>
@endsection
