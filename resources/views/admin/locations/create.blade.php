@extends('layouts.admin')

@section('heading', 'Add location')

@section('content')
    <form method="POST" action="{{ route('admin.locations.store') }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @include('admin.locations.partials.form')
        <button type="submit" class="btn-primary">Save location</button>
    </form>
@endsection
