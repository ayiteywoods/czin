@extends('layouts.admin')

@section('heading', 'Add staff shift')

@section('content')
    <form method="POST" action="{{ route('admin.staff-shifts.store') }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @include('admin.staff-shifts.partials.form')
        <button type="submit" class="btn-primary">Save shift</button>
    </form>
@endsection
