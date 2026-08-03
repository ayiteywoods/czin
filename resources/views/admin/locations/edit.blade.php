@extends('layouts.admin')

@section('heading', 'Edit location')

@section('content')
    <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.locations.partials.form', ['location' => $location])
        <button type="submit" class="btn-primary">Update location</button>
    </form>
@endsection
