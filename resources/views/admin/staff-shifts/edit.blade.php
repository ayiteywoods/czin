@extends('layouts.admin')

@section('heading', 'Edit staff shift')

@section('content')
    <form method="POST" action="{{ route('admin.staff-shifts.update', $shift) }}" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('admin.staff-shifts.partials.form', ['shift' => $shift])
        <button type="submit" class="btn-primary">Update shift</button>
    </form>
@endsection
