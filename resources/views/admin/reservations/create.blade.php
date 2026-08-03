@extends('layouts.admin')

@section('heading', 'Add reservation')

@section('content')
    <form method="POST" action="{{ route('admin.reservations.store') }}" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('admin.reservations.partials.form')
        <button type="submit" class="btn-primary">Save reservation</button>
    </form>
@endsection
